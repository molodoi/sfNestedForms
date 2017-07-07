$(document).on('change', '#appbundle_post_region, #appbundle_post_department', function(){
    let $field = $(this);
    let $regionField = $('#appbundle_post_region');
    let $form = $field.closest('form');
    let target = '#' + $field.attr('id').replace('department','city').replace('region','department');
    let data = {};

    data [$regionField.attr('name')] = $regionField.val();
    data [$field.attr('name')] = $field.val();
    $.post($form.attr('action'), data).then(fucntion(data){
        let $input = $(data).find(target);
        $(target).replaceWith($input);
    });
});