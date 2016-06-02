$(function () {
    // Varying modal based on modal id show event
    $('#delete-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var action = button.data('action') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this);
        if(action !=undefined)
        {
            modal.find('form').attr('action',action);
        }

    })
});