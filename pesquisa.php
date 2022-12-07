<?php

require_once("custom/php/common.php");

if(is_user_logged_in() && current_user_can("search")){

    if(!isset($_REQUEST['estado'])){
        echo"<h3>Pesquisa - escolher item</h3>";

        $nomeTipoItem = "SELECT * FROM item_type";
        
        $res_nti = mysqli_query($link,$nomeTipoItem);

        if(mysqli_num_rows($res_nti)>0){
            while($row2 = mysqli_fetch_assoc($res_nti)){
                $nomeItem = "SELECT * FROM item WHERE item_type_id=".$row2["id"]."";
                $res_ni = mysqli_query($link,$nomeItem);
                echo "<ul> 
                    <li>".preg_replace('/_/i',' ',$row2["name"])."</li>
                    <ul>";
                    while ($row3 = mysqli_fetch_assoc($res_ni)){
                        $url3 = "?estado=escolha&item=".$row3["id"]."";
                        echo"
                            <li> [<a href='$current_page.$url3' >".$row3["name"]."</a>]</li>
                    ";
                    } echo "</ul> </ul>";
            }
        }
    }

    elseif($_REQUEST['estado']='escolha'){

        $queryNomeItem="SELECT name FROM item WHERE id=".$_REQUEST['item']."";
        $resultNomeItem=mysqli_query($link,$queryNomeItem);
        $nomeDoItem=mysqli_fetch_assoc($resultNomeItem);

        $_SESSION['id']=$_REQUEST['item'];
        $_SESSION['nomde_do_item']=$nomeDoItem['name'];

        echo"<form action='' method='POST'>
                <table>
                    <thead>
                        <tr>
                            <th style='text-align:center'>obter</th>
                        </tr>
                    </thead>

                </table>
                <table>
                    <thead>
                        <tr>
                            <th style='text-align:center'>filtro</th>
                        </tr>
                    </thead>

                </table>
            </form>";
        button_voltar();
    }

    elseif($_REQUEST['estado']='escolher_filtros'){

    }
}
else{
    echo"<p style='color:red;'>Não tem autorização para aceder a esta página</p>";
    button_voltar();
}
?>