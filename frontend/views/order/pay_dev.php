<?php include 'pay_detail.php'; ?>

<script>
    function callpay() {
        var order_id = $('#order_id').val();
        window.location.href = '/order/paid?order_id=' + order_id;
    }
</script>