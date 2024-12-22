<?php
# ============================================================
# FUNCTION UI INPUTS
# ============================================================
function create_ui($field, $value, $id_sesi, $field_title = null, $value_class = null, $is_textarea = false)
{
  if ($field_title === null) {
    $field_title = $field;
  }

  $field_title_edit = $field_title ? $field_title : $field;

  $input = $is_textarea ? "
    <textarea class='form-control input_editable' id=$field" . "__$id_sesi rows=4>$value</textarea>
  " : "
    <input class='form-control input_editable' id=$field" . "__$id_sesi value='$value' />
  ";

  $ket = '';
  $kets = [
    'tags' => "pisahkan tag dengan koma, tanpa karakter spesial, disarankan tag terdiri dari dua kata, semisal: \"definisi html\", \"manfaat html\", \"struktur html\", dll",
  ];
  $ket = $kets[$field] ?? '';

  return "
    <div class='create_ui tengah'>
      <div class=ui_view>
        <div class='kecil miring abu mt3 mb1 proper'>$field_title</div>
        <div class='mb3 $value_class' id=isi_lama__$field" . "__$id_sesi>$value</div>
      </div>
      <div class='ui_edit'>
        <div class='wadah gradasi-kuning'>
          <div class=row>
            <div class='col-sm-3 mb1 proper left'>$field_title_edit:</div>
            <div class='col-sm-9 mb1 left'>
              $input
              <div class='abu f12 miring mt1'>$ket</div>
              <button class='hideit btn btn-sm btn-primary btn_save mt1' id=btn_save__$field" . "__$id_sesi>Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  ";
}
