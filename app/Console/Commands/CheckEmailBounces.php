<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class CheckEmailBounces extends Command
{
    protected $signature = 'email:check-bounces
        {--imap-host= : IMAP host (override .env)}
        {--imap-port=993 : IMAP port}
        {--imap-username= : IMAP username}
        {--imap-password= : IMAP password}
        {--mailbox=INBOX : Mailbox to check}
        {--since= : Only check messages since this date (Y-m-d)}
        {--dry-run : Don\'t update database, just show what would be changed}';

    protected $description = 'Check IMAP mailbox for bounced email messages and update EmailLog status';

    public function handle(): int
    {
        $host = $this->option('imap-host') ?: env('BOUNCE_IMAP_HOST');
        $port = $this->option('imap-port') ?: env('BOUNCE_IMAP_PORT', '993');
        $username = $this->option('imap-username') ?: env('BOUNCE_IMAP_USERNAME');
        $password = $this->option('imap-password') ?: env('BOUNCE_IMAP_PASSWORD');
        $mailbox = $this->option('mailbox') ?: env('BOUNCE_IMAP_MAILBOX', 'INBOX');
        $since = $this->option('since');
        $dryRun = $this->option('dry-run');

        if (!$host || !$username || !$password) {
            $this->error('Bounce IMAP not configured. Set BOUNCE_IMAP_HOST, BOUNCE_IMAP_USERNAME, BOUNCE_IMAP_PASSWORD in .env');
            $this->line('Or use --imap-host, --imap-username, --imap-password options.');
            return Command::FAILURE;
        }

        $this->info("Connecting to {$host}:{$port} as {$username}...");

        try {
            $cm = new ClientManager();
            $client = $cm->make([
                'host'          => $host,
                'port'          => $port,
                'encryption'    => 'ssl',
                'validate_cert' => true,
                'username'      => $username,
                'password'      => $password,
                'protocol'      => 'imap',
            ]);

            $client->connect();
            $this->info('✅ Connected successfully.');
        } catch (ConnectionFailedException $e) {
            $this->error('Connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Open the mailbox (folder)
        $folder = $client->getFolder($mailbox);
        if (!$folder) {
            $this->error("Mailbox '{$mailbox}' not found.");
            $client->disconnect();
            return Command::FAILURE;
        }

        $this->info("Checking mailbox: {$mailbox}");

        // Build query
        $query = $folder->messages()->unseen(); // only unseen messages
        if ($since) {
            $query->since(\Carbon\Carbon::parse($since));
        }

        $messages = $query->get();
        $count = $messages->count();
        $this->info("Found {$count} unseen message(s).");

        if ($count === 0) {
            $client->disconnect();
            $this->info('No bounces to process.');
            return Command::SUCCESS;
        }

        $bouncedEmails = [];
        $processed = 0;

        foreach ($messages as $message) {
            try {
                $subject = $message->getSubject();
                $from = $message->getFrom();
                $body = $message->getTextBody() ?: $message->getHTMLBody();

                $fromMail = '';
                if ($from && $from->first()) {
                    $fromMail = $from->first()->mail;
                }

                $this->line("  📧 Subject: {$subject}");
                $this->line("     From: {$fromMail}");

                // Try to extract the original recipient that bounced
                $bouncedRecipient = $this->extractBouncedRecipient($subject, $body);

                if ($bouncedRecipient) {
                    $bouncedEmails[] = $bouncedRecipient;

                    // Update email logs with this recipient
                    $logs = EmailLog::where('recipient_email', $bouncedRecipient)
                        ->where('status', 'sent')
                        ->latest()
                        ->get();

                    if ($logs->isNotEmpty()) {
                        foreach ($logs as $log) {
                            if ($dryRun) {
                                $this->line("     ➜ WOULD UPDATE: Log #{$log->id} ({$log->recipient_email}) → bounced");
                            } else {
                                $log->update([
                                    'status' => 'bounced',
                                    'error_message' => 'Bounced: ' . $subject,
                                ]);
                                $this->line("     ✅ Updated Log #{$log->id} → bounced");
                            }
                            $processed++;
                        }
                    } else {
                        $this->line("     ⚠ No sent log found for {$bouncedRecipient}");
                    }
                } else {
                    $this->line('     ⚠ Could not extract bounced recipient');
                }

                // Mark as seen so we don't process again
                if (!$dryRun) {
                    $message->setFlag('SEEN');
                }

            } catch (\Exception $e) {
                $this->warn("     Error processing message: " . $e->getMessage());
            }
        }

        $client->disconnect();

        if ($dryRun) {
            $this->info("--- DRY RUN --- Would have updated {$processed} log(s).");
        } else {
            $this->info("✅ Processed {$count} message(s), updated {$processed} log(s).");
        }

        return Command::SUCCESS;
    }

    /**
     * Extract the original bounced recipient email from bounce message subject/body.
     * Handles common bounce formats:
     *   - "mailbox <email> was unavailable"
     *   - "Undelivered Mail Returned to Sender"
     *   - "Delivery Status Notification (Failure)"
     *   - DSN (Delivery Status Notification) body with Original-Recipient field
     */
    private function extractBouncedRecipient(string $subject, string $body): ?string
    {
        // Pattern 1: DSN body with Original-Recipient: rfc822; email@example.com
        if (preg_match('/Original-Recipient:\s*rfc822;\s*([^\s;]+)/i', $body, $m)) {
            return strtolower(trim($m[1]));
        }

        // Pattern 2: Final-Recipient: rfc822; email@example.com
        if (preg_match('/Final-Recipient:\s*rfc822;\s*([^\s;]+)/i', $body, $m)) {
            return strtolower(trim($m[1]));
        }

        // Pattern 3: Subject contains " <email@example.com> " or similar
        if (preg_match('/<([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>/', $subject, $m)) {
            return strtolower($m[1]);
        }

        // Pattern 4: Body contains "Original address: email@example.com"
        if (preg_match('/Original (?:address|recipient)[:\s]*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $body, $m)) {
            return strtolower($m[1]);
        }

        // Pattern 5: Body contains email in angle brackets in a delivery failure context
        if (preg_match('/(?:delivery|failure|bounce|undelivered).*<([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>/is', $body, $m)) {
            return strtolower($m[1]);
        }

        // Pattern 6: Any email mentioned in subject line (catch-all, less reliable)
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $body, $m)) {
            return strtolower($m[0]);
        }

        return null;
    }
}
