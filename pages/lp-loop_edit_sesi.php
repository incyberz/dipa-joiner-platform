<?php
if ($id_role == 2) {
  $up_disabled = "<span onclick='alert(`Tidak dapat Move-Up Pertemuan Pertama.`)'>$img_up_disabled</span>";
  $form_set_up = "
    <form method=post style='display:inline'>
      <button class='btn-transparan' onclick='return confirm(`Move Up urutan sesi ini?`)' name=btn_move_up value=$id_sesi>$img_up</button>
    </form>
  ";
  $form = $no_sesi <= 1 ? $up_disabled : $form_set_up;
  $edit_sesi = "
    <div class='flexy flex-between desktop_only mb2 f10 abu'>
      <div>
        $form
      </div>
      <div>id. $id_sesi</div>
      <div>
        <span class=mode_edit id=mode_edit__$id_sesi >$img_edit</span>
      </div>
    </div>
  ";
}
