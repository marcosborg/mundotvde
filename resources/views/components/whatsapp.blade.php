<div class="whatsapp-container" onclick="openWhatsApp()">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
</div>


<style>
    .whatsapp-container {
        background: #ffffff;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        text-align: center;
        position: fixed;
        box-shadow: 0px 0px 5px 5px #25D366;
        bottom: 10%;
        cursor: pointer;
    }

    .whatsapp-container i {
        color: #25D366;
        font-size: 50px;
        line-height: 80px;
    }
</style>
<script>
    function openWhatsApp() {
        window.open('https://api.whatsapp.com/send?phone=351964028006', '_blank');
    }
</script>