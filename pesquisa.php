<?php

require_once("custom/php/common.php");


function operador(){

}

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

    elseif($_REQUEST['estado']=='escolha'){

        $queryNomeItem="SELECT name FROM item WHERE id=".$_REQUEST['item']."";
        $resultNomeItem=mysqli_query($link,$queryNomeItem);
        $nomeDoItem=mysqli_fetch_assoc($resultNomeItem);

        $_SESSION['id']=$_REQUEST['item'];
        $_SESSION['nomde_do_item']=$nomeDoItem['name'];

        echo"<form action='' method='POST'>
                <table>
                    <thead>
                        <tr>
                            <th>Atributo</th>
                            <th>Obter</th>
                            <th>Filtro</th>
                        </tr>
                    </thead>
                    <tbody>";
                        $queryAtrib="SHOW COLUMNS FROM child";
                        $resultAtrib=mysqli_query($link,$queryAtrib);
                        while($atributos=mysqli_fetch_assoc($resultAtrib)){
                            echo"<tr>
                                    <td>".$atributos['Field']."</td>
                                    <td><input type='checkbox' name='obter_atributo[]' value='".$atributos['Field']."'></td>
                                    <td><input type='checkbox' name='filtro_atributo[]' value='".$atributos['Field']."'></td>
                                </tr>";
                        }  
                       
            echo    "</tbody>
                </table>
                <table>
                    <thead>
                        <tr>
                            <th>Subitem</th>
                            <th>Obter</th>
                            <th>Filtro</th>
                        </tr>
                    </thead>
                    <tbody>";
                        $querySubitemId="SELECT id,name FROM subitem WHERE item_id=".$_SESSION['id']."";
                        $resultSubitemId=mysqli_query($link,$querySubitemId);
                        while($subitem=mysqli_fetch_assoc($resultSubitemId)){
                            echo"<tr>
                                    <td>".$subitem['name']."</td>
                                    <td><input type='checkbox' name='obter_subitem[]' value='".$subitem['id']."'></td>
                                    <td><input type='checkbox' name='filtro_subitem[]' value='".$subitem['id']."'></td>
                                </tr>";
                        }
                echo"</tbody>
                </table>
                <input type='hidden' name='estado' value='escolher_filtros'>
                <input type='submit' value='Submeter'>
            </form>";
        button_voltar();
    }

    elseif($_REQUEST['estado']=='escolher_filtros'){

        $_SESSION['nome_atributo_obter']=$_REQUEST['obter_atributo'];
        $_SESSION['nome_atributo_filtro']=$_REQUEST['filtro_atributo'];
        $_SESSION['id_subitem_obter']=$_REQUEST['obter_subitem'];
        $_SESSION['id_subitem_filtro']=$_REQUEST['filtro_subitem'];

        /*print_r($_SESSION['nome_atributo_obter']);
        print_r($_SESSION['nome_atributo_filtro']);
        print_r($_SESSION['id_subitem_obter']);
        print_r($_SESSION['id_subitem_filtro']);*/

        //$nomes_subitem_filtro=array();

        //ordena alfabeticamnete
        sort($_SESSION['nome_atributo_obter']);

        echo"<form action='' method='POST'>
                <ul>";
                    foreach($_SESSION['nome_atributo_obter'] as $nomeAtributo){
                        echo"<li>$nomeAtributo";
                        if (in_array($nomeAtributo,$_SESSION['nome_atributo_filtro'])){
                            echo"<div class='operador'> Operador </div>";
                            //echo$nomeAtributo;
                        }
                        echo"</li>";
                    }
            echo"</ul>
            </form>
        ";

        button_voltar();
    }

    elseif($_REQUEST['estado']=='execucao'){
        
    }
}
else{
    echo"<p style='color:red;'>Não tem autorização para aceder a esta página</p>";
    button_voltar();
}
?>