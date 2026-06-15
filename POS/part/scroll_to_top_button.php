<button id="goTopBtn" class="btn btn-primary rounded-circle z-0 position-fixed b-80px" style="bottom: 80px; right: 20px;">
    <i class="fas fa-arrow-up"></i>
</button>
<script>
    $(document).ready(function() {
        $("#goTopBtn").fadeOut()
    });
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $("#goTopBtn").fadeIn();
        } else {
            $("#goTopBtn").fadeOut();
        }
    });
    $("#goTopBtn").click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 100);
    });
</script>