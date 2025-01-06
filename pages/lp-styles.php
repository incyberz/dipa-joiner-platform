<style>
  .blok_list_sesi {
    display: grid;
    grid-template-columns: 100px auto;
    grid-gap: 10px
  }

  @media (max-width:450px) {
    .blok_list_sesi {
      display: block;
    }
  }

  .nav_lp {
    border: solid 3px white;
    transition: .2s;

  }

  .nav_lp:hover {
    border: solid 3px lightblue;
    background: linear-gradient(#fcf, pink);
    font-weight: bold;
    padding-left: 12px;
    padding-right: 12px;
  }

  .nav_lp_selected {
    border: solid 3px blue;
  }

  .nav_lp_active {
    border: solid 3px lightskyblue;
    font-weight: bold;
    color: blue;
  }

  .editable {
    background: white;
  }

  .ui_edit {
    display: none;
  }

  .icon_bahan_ajar_disabled {
    opacity: 20%;
    -webkit-filter: grayscale();
  }

  .icon_bahan_ajar {
    height: 50px;
    width: 50px;
    object-fit: cover;
    transition: .2s;
  }

  .icon_bahan_ajar:hover {
    transform: scale(1.1)
  }

  #laporkan_error__toggle {
    transition: .2s;
  }

  #laporkan_error__toggle:hover {
    color: red;
    letter-spacing: .5px;
  }

  #form_add__toggle {
    transition: .2s;
  }

  #form_add__toggle:hover {
    color: green;
    letter-spacing: .5px;
  }
</style>