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
        justify-content: end;

        
        
        transition-duration: 500ms;

        /*
        position: sticky;
        top: 0;
        z-index: 1000;
        animation: headerFijo linear both;
        animation-timeline: scroll(root);
        animation-range: 0 100px;
        */
    }

    .logo {
        position: absolute;
        right: 50%;
        transform: translateX(50%);

        height: 65%;
        overflow: visible;
        color: white;

        & path {
            height: 100%;
        }
    }

    .opciones {
        display: flex;
        gap: 20px;

        & .icon {
            transition-duration: 500ms;
            &:hover {
                transform: scale(110%);
            }
        }
    }


    @media (max-width: 950px){
        .logo{
            left: 10px;
        }
    }

    @media (max-width: 500px){
        .logo{
            left: 0px;
        }
    }

    @keyframes headerFijo {
        to {
            height: 80px;
        }
    }
</style>
    
<header>
    <?php include BASE_DIR . "/public/img/logo.svg"?>
    <div class="opciones">
        <a style="text-decoration: none;" href="/index.php">
             <?php include BASE_DIR . "/public/img/home.svg"?>
        </a>
    </div>
</header>