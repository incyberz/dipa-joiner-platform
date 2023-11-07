    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>


Full working sample actually is:


<br><br>
<label>encrypted</label>
<div id="demo1"></div>
<br>

<label>decrypted</label>
<div id="demo2"></div>

<br>
<label>Actual Message</label>
<div id="demo3"></div>


<script>
  let encrypted = CryptoJS.AES.encrypt("Message", "Secret Passphrase");
//U2FsdGVkX18ZUVvShFSES21qHsQEqZXMxQ9zgHy+bu0=

let decrypted = CryptoJS.AES.decrypt(encrypted, "Secret Passphrase");
//4d657373616765


document.getElementById("demo1").innerHTML = encrypted;
document.getElementById("demo2").innerHTML = decrypted;
document.getElementById("demo3").innerHTML = decrypted.toString(CryptoJS.enc.Utf8);
</script>