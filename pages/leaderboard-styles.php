<style>
  .juara-1 {
    background: linear-gradient(#220000cc, black);
  }

  .juara-2,
  .juara-3 {
    background: linear-gradient(#000011cc, black);
  }

  .shimmer {
    font-size: 60px;
    font-weight: 300;
    margin: 0 auto;
    text-align: center;
  }

  .shimmer-second {
    font-size: 40px;
  }

  .shimmer-third {
    font-size: 35px;
  }

  .shimmer-st {
    font-size: 20px;
  }

  .shimmer {
    text-align: center;
    color: rgba(255, 255, 255, 0.1);
    background: -webkit-gradient(linear,
        left top,
        right top,
        from(#222),
        to(#222),
        color-stop(0.5, #fff));
    background: -moz-gradient(linear,
        left top,
        right top,
        from(#222),
        to(#222),
        color-stop(0.5, #fff));
    background: gradient(linear,
        left top,
        right top,
        from(#222),
        to(#222),
        color-stop(0.5, #fff));
    -webkit-background-size: 125px 100%;
    -moz-background-size: 125px 100%;
    background-size: 125px 100%;
    -webkit-background-clip: text;
    -moz-background-clip: text;
    background-clip: text;
    -webkit-animation-name: shimmer;
    -moz-animation-name: shimmer;
    animation-name: shimmer;
    -webkit-animation-duration: 2s;
    -moz-animation-duration: 2s;
    animation-duration: 2s;
    -webkit-animation-iteration-count: infinite;
    -moz-animation-iteration-count: infinite;
    animation-iteration-count: infinite;
    background-repeat: no-repeat;
    background-position: 0 0;
    background-color: #222;
  }

  @-moz-keyframes shimmer {
    0% {
      background-position: top left;
    }

    100% {
      background-position: top right;
    }
  }

  @-webkit-keyframes shimmer {
    0% {
      background-position: top left;
    }

    100% {
      background-position: top right;
    }
  }

  @-o-keyframes shimmer {
    0% {
      background-position: top left;
    }

    100% {
      background-position: top right;
    }
  }

  @keyframes shimmer {
    0% {
      background-position: top left;
    }

    100% {
      background-position: top right;
    }
  }
</style>