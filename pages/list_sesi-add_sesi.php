<?php
$add_sesi = $id_role != 2 ? '' : "
<form method=post data-aos=fade>
  <div class='kanan'>
    <button class='btn btn-success' onclick='return confirm('Add Sesi untuk Room ini?')' name=btn_add_sesi>Add Sesi</button>
  </div>
</form>
";
