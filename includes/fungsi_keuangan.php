<?php
include 'frp.php';
include 'terbilang.php';









?>
<script>
  const rupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR"
    }).format(number);
  }
</script>