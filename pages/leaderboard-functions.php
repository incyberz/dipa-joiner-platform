<?php
function div_best($best, $div_peserta, $stars, $title, $desc, $gradasi = '')
{
  return "
    <div class='col-lg-6'  data-aos='fade-up'>
      <div class='wadah tengah $gradasi'>
        <h4 class='f16'>
          $stars  
          <span class='upper green bold' style='display:inline-block; margin-top:15px'>
            $title
          </span> 
          $stars 
        </h4>
        <p class=abu>
          $desc
        </p>
        <div class='flexy flex-center center border-bottom pb2 div_peserta'>
          $div_peserta
        </div>
          <a href='?leaderboard&best=$best'>view more</a>
      </div>
    </div>  
  ";
}
