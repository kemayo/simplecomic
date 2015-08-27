$("input.datetime").each(function() {
    // format: YYYY-MM-DD HH:MM:SS
    var $this = $(this);
    var datetime = $this.val().split(' ');
    $this.hide();
    var date_input = $('<input type="date"/>').val(datetime[0]);//dateinput({value: datetime[0]});
    var time_input = $('<input type="time"/>').val(datetime[1]);
    $this.after(time_input).after(date_input);
    var update_date = function() {
        $this.val(date_input.val() + ' ' + time_input.val());
    };
    date_input.change(update_date);
    time_input.change(update_date);
    $this.closest('form').bind('submit', update_date);
});
$("button.delete").click(function(e) {
    if(!confirm("Are you sure you want to delete this?")) {
        e.preventDefault();
    }
});