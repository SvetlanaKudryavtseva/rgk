/*
You can use this file with your scripts.
It will not be overwritten when you upgrade solution.
*/
$(document).ready(function() {
    $(".email-block").on("click", function() {
        $('#email-dropdown').toggle();
    });

    /* HIDE BLOCK "OFTEN SEARCH" ON CATALOG PARTICULAR SECTION PAGE IF THERE ARE NO TAGS FOR THIS SECTION */
    if ($('.often-tags__items').html().trim() === '') {
        $('.often-tags__items').parent('.often-tags').hide();
    }
});