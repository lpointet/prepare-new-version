(function($) {
    $(function() {
        $('#the-list').on('click', 'a.editinline', function(){
            var id = inlineEditPost.getId(this),
                rowData = $('#inline_'+id);

            $('#edit-' + id).addClass('status-' + $('._status', rowData).text());
        });
    });
})(jQuery);