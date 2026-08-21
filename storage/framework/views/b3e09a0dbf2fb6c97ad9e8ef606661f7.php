
<?php if (! $__env->hasRenderedOnce('d5ba0646-8751-4bf5-9ba6-3889a10dd01e')): $__env->markAsRenderedOnce('d5ba0646-8751-4bf5-9ba6-3889a10dd01e'); ?>
<script>
// Generic collapsible checkbox-dropdown used by the Profile & Source filters.
(function () {
    function initFilterDropdown(root) {
        var toggle = root.querySelector('[data-dropdown-toggle]');
        var panel = root.querySelector('[data-dropdown-panel]');
        var label = root.querySelector('[data-dropdown-label]');
        var clearBtn = root.querySelector('[data-dropdown-clear]');
        var emptyLabel = root.getAttribute('data-empty-label') || 'All';
        if (!toggle || !panel) return;

        function updateLabel() {
            var n = root.querySelectorAll('input[type="checkbox"]:checked').length;
            label.textContent = n > 0 ? (n + ' selected') : emptyLabel;
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.filter-dropdown').forEach(function (d) {
                if (d !== root) {
                    var p = d.querySelector('[data-dropdown-panel]');
                    if (p) p.classList.add('hidden');
                }
            });
            panel.classList.toggle('hidden');
        });

        if (clearBtn) clearBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            root.querySelectorAll('input[type="checkbox"]').forEach(function (cb) { cb.checked = false; });
            updateLabel();
        });

        root.querySelectorAll('input[type="checkbox"]').forEach(function (cb) { cb.addEventListener('change', updateLabel); });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) panel.classList.add('hidden');
        });

        updateLabel();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.filter-dropdown').forEach(initFilterDropdown);
    });
})();
</script>
<?php endif; ?>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/partials/filter-dropdown-script.blade.php ENDPATH**/ ?>