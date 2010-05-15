$("input.datetime").each(function() {
    // format: YYYY-MM-DD HH:MM:SS
    var $this = $(this);
    var datetime = $this.val().split(' ');
    $this.hide();
    var date_input = $('<input type="date" />').val(datetime[0]);//dateinput({value: datetime[0]});
    var time_input = $('<input>').val(datetime[1]);
    $this.after(time_input).after(date_input);
    date_input.dateinput({format: 'yyyy-mm-dd'});
    var update_date = function() {
        $this.val(date_input.val() + ' ' + time_input.val());
    };
    date_input.bind('onHide', update_date);
    time_input.change(update_date);
    $this.closest('form').submit(update_date);
    $this.prev('label').append($(' <a href="#" title="hide calendar">(x)</a>)').click(function(e) {
        e.preventDefault();
        update_date();
        date_input.remove();
        time_input.remove();
        $this.show();
        $(this).remove();
    }))
});