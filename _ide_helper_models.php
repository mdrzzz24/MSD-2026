<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEmail whereUpdatedAt($value)
 */
	class AdminEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agenda_item_id
 * @property int|null $registrant_id
 * @property string $name
 * @property string $email
 * @property int|null $rating
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AgendaItem $agendaItem
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaFeedbackAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Registrant|null $registrant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereAgendaItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereRegistrantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedback whereUpdatedAt($value)
 */
	class AgendaFeedback extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agenda_feedback_id
 * @property int $agenda_item_question_id
 * @property string|null $answer_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AgendaFeedback $feedback
 * @property-read \App\Models\AgendaItemQuestion|null $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereAgendaFeedbackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereAgendaItemQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereAnswerValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaFeedbackAnswer whereUpdatedAt($value)
 */
	class AgendaFeedbackAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $speaker_name
 * @property string|null $speaker_title
 * @property string|null $speaker_photo
 * @property string|null $speaker2_name
 * @property string|null $speaker2_title
 * @property string|null $speaker2_photo
 * @property string|null $key_highlights
 * @property string|null $category
 * @property string|null $agenda_type track, workshop, keynote, etc.
 * @property int|null $workshop_id
 * @property int|null $track_id
 * @property string|null $room
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $date
 * @property int $order
 * @property int $rowspan
 * @property int $colspan
 * @property bool $is_registrable
 * @property int $capacity
 * @property bool $feedback_enabled
 * @property int $registration_open
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaFeedback> $feedback
 * @property-read int|null $feedback_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaItemQuestion> $feedbackQuestions
 * @property-read int|null $feedback_questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registrant> $registrants
 * @property-read int|null $registrants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Speaker> $speakers
 * @property-read int|null $speakers_count
 * @property-read \App\Models\Track|null $track
 * @property-read \App\Models\Workshop|null $workshop
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereAgendaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereColspan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereFeedbackEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereIsRegistrable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereKeyHighlights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereRegistrationOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereRowspan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeaker2Name($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeaker2Photo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeaker2Title($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeakerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeakerPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereSpeakerTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItem whereWorkshopId($value)
 */
	class AgendaItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agenda_item_id
 * @property int|null $source_template_id
 * @property int|null $source_template_question_id
 * @property string $question_text
 * @property string $question_type
 * @property array<array-key, mixed>|null $options
 * @property int|null $parent_question_id
 * @property string|null $trigger_value
 * @property int $order
 * @property bool $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AgendaItem|null $agendaItem
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaFeedbackAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AgendaItemQuestion> $children
 * @property-read int|null $children_count
 * @property-read AgendaItemQuestion|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereAgendaItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereParentQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereSourceTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereSourceTemplateQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereTriggerValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaItemQuestion whereUpdatedAt($value)
 */
	class AgendaItemQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $email_template_id
 * @property int|null $registrant_id
 * @property string|null $template_type
 * @property string $recipient_email
 * @property string|null $recipient_name
 * @property string $subject
 * @property string $status
 * @property string|null $error_message
 * @property string|null $html_content
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Registrant|null $registrant
 * @property-read \App\Models\EmailTemplate|null $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereEmailTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereHtmlContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereRecipientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereRegistrantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereUpdatedAt($value)
 */
	class EmailLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property string $subject
 * @property string $html_content
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate approval()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate rejection()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereHtmlContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUpdatedAt($value)
 */
	class EmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackTemplateQuestion> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplate whereUpdatedAt($value)
 */
	class FeedbackTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $template_id
 * @property string $question_text
 * @property string $question_type
 * @property array<array-key, mixed>|null $options
 * @property int|null $parent_question_id
 * @property string|null $trigger_value
 * @property int $order
 * @property bool $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeedbackTemplateQuestion> $children
 * @property-read int|null $children_count
 * @property-read FeedbackTemplateQuestion|null $parent
 * @property-read \App\Models\FeedbackTemplate|null $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereParentQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereTriggerValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackTemplateQuestion whereUpdatedAt($value)
 */
	class FeedbackTemplateQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Floor whereUpdatedAt($value)
 */
	class Floor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed>|null $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string|null $owner_name
 * @property string|null $description
 * @property int $max_uses
 * @property int $uses_count
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registrant> $registrants
 * @property-read int|null $registrants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereMaxUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereUsesCount($value)
 */
	class ReferralCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $job_title
 * @property string|null $job_role
 * @property string|null $company
 * @property string $email
 * @property string|null $phone
 * @property string|null $organization
 * @property string|null $industry
 * @property bool $attended_before
 * @property string|null $referral_source
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_content
 * @property string|null $employees
 * @property bool $gdpr
 * @property string|null $unique_code
 * @property string|null $referral_code
 * @property string|null $password
 * @property string|null $plain_password
 * @property string|null $qr_token
 * @property string|null $remember_token
 * @property string|null $notes
 * @property string $status
 * @property string|null $interest
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $referral_code_id
 * @property int|null $approved_by
 * @property int|null $rejected_by
 * @property int|null $assigned_to
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaItem> $agendaItems
 * @property-read int|null $agenda_items_count
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\User|null $assignedAdmin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailLog> $emailLogs
 * @property-read int|null $email_logs_count
 * @property-read string $display_name
 * @property-read string $qr_checkin_url
 * @property-read string $qr_code_url
 * @property-read string $qr_share_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ReferralCode|null $referralCode
 * @property-read \App\Models\User|null $rejecter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workshop> $workshopWaitlists
 * @property-read int|null $workshop_waitlists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workshop> $workshops
 * @property-read int|null $workshops_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereAttendedBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereEmployees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereGdpr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereInterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereJobRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant wherePlainPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereQrToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereReferralCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereReferralSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUniqueCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Registrant whereUtmSource($value)
 */
	class Registrant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $floor
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $floor_id
 * @property-read \App\Models\Floor|null $floorRelation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereFloorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $company
 * @property string|null $photo
 * @property string|null $bio
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaItem> $agendaItems
 * @property-read int|null $agenda_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Speaker whereUpdatedAt($value)
 */
	class Speaker extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $start_time
 * @property string $end_time
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TimeSlot whereUpdatedAt($value)
 */
	class TimeSlot extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaItem> $agendaItems
 * @property-read int|null $agenda_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Track whereUpdatedAt($value)
 */
	class Track extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property bool $is_admin
 * @property string $role
 * @property array<array-key, mixed>|null $permissions
 * @property int|null $group_id
 * @property string|null $setup_token
 * @property \Illuminate\Support\Carbon|null $setup_token_expires_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registrant> $assignedRegistrants
 * @property-read int|null $assigned_registrants_count
 * @property-read \App\Models\Group|null $group
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSetupToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSetupTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $base_url
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_campaign
 * @property string|null $utm_content
 * @property string|null $full_url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $sharedWith
 * @property-read int|null $shared_with_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereBaseUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereFullUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UtmLink whereUtmSource($value)
 */
	class UtmLink extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $room
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int $capacity
 * @property bool $registration_open
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgendaItem> $agendaItems
 * @property-read int|null $agenda_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registrant> $registrants
 * @property-read int|null $registrants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registrant> $waitlist
 * @property-read int|null $waitlist_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereRegistrationOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workshop whereUpdatedAt($value)
 */
	class Workshop extends \Eloquent {}
}

