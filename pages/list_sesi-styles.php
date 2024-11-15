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

    .str_fiturs {
      display: block;
    }

    .str_fiturs div {
      width: 100%;
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
</style>