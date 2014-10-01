$(document).ready(function() {

    $('.textext')
        .textext({
            plugins : 'tags autocomplete'
        })
        .bind('getSuggestions', function(e, data)
        {
            var list = ['Salut','Cest','Cool'],
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }
            );
        })
        ;
});
