$(document).ready(function() {
    // Initialize Sortable for each category section
    $('.plo-category-section').each(function() {
        new Sortable(this, {
            group: {
                name: 'plo-list',
                // Restrict movement between different categories
                pull: false,
                put: false
            },
            animation: 150,
            handle: '.drag-handle', // Drag handle class
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                // Update hidden input fields for new order
                updatePLOOrder(evt.to);
            }
        });
    });

    function updatePLOOrder(container) {
        // Get all PLO sections to update the complete order
        const allPLOSections = $('.plo-category-section');
        const allPLOIds = [];

        // Clear all existing position inputs
        $('input[name="plos_pos[]"]').remove();

        // Collect PLO IDs in their current order from all sections
        allPLOSections.each(function() {
            const rows = $(this).find('tr[data-plo-id]');
            rows.each(function() {
                const ploId = $(this).data('plo-id');
                allPLOIds.push(ploId);

                // Create a new hidden input for the PLO's position
                const input = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'plos_pos[]')
                    .val(ploId);
                $(this).append(input);
            });
        });

        // Enable the save button since changes were made
        $('button[type="submit"]').prop('disabled', false)
            .addClass('btn-success')
            .removeClass('btn-secondary');
    }

    // Initially disable save button until changes are made
    $('button[type="submit"]').prop('disabled', true)
        .addClass('btn-secondary')
        .removeClass('btn-success');

    // Update all category sections on page load to ensure proper order
    updatePLOOrder();
});