<style>
    header {
        background-color: #2fa2ef;
        background-image: linear-gradient(3deg, #0046ab, #2467c6, #388ae3, #2fa2ef);
        background-size: 200%;
        background-position: center;
        height: 110px;
        padding: 20px;
        display: flex;
        align-items: center;
        padding: 0 15%;
        position: relative;
        justify-content: center;

        
        
        transition-duration: 500ms;
    }

    a {
        height: 65%;
        overflow: visible;
        color: white;

        & .logo {
            height: 100%;
        }
    }
</style>
    
<header>
  <a style="text-decoration: none;" href="/index.php">
    <?php include BASE_DIR . "/public/img/logo.svg"?>
  </a>
</header>