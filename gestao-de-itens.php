<?php 

require_once("custom/php/common.php");

if(!$link){
    echo "Connection error";
}
else{
    //echo "Connection succeded";
}

//Não sei fazer variáveis de sessão

if(is_user_logged_in() && current_user_can("manage_itens")){
    if(!isset($_REQUEST["estado"])){

        $queryTabelaItem = "SELECT * FROM item"; //Query da tabela item para verificar que existem itens
        $resultTabelaItem = mysqli_query($link, $queryTabelaItem);

        if(mysqli_num_rows($resultTabelaItem) > 0){ //Se existe itens

            echo "<table>
                <tr>
                    <th>Tipo de item</th>
                    <th>ID</th>
                    <th>Nome do item</th>
                    <th>Estado</th>
                    <th>Ação</th>
                </tr>";
                    $queryTipos = "SELECT item_type.id, item_type.name 
                    FROM item_type"; //Query dos tipos de itens
                    $resultTipos = mysqli_query($link, $queryTipos);
                    while($rowTipos = mysqli_fetch_assoc($resultTipos)){

                        $queryItens = "SELECT item.id, item.name, item.state 
                        FROM item, item_type 
                        WHERE item_type.id = item.item_type_id AND item.item_type_id =" .$rowTipos['id']. ""; //Query dos itens do tipo de item em questão
                        $resultItens = mysqli_query($link, $queryItens);

                        if(mysqli_num_rows($resultItens) > 0){ //Se exitem itens para o tipo de item em questão

                        echo "<tr><td rowspan=" .mysqli_num_rows($resultItens). ">" .$rowTipos["name"]. "</td>";

                        while($rowItens = mysqli_fetch_assoc($resultItens)){
                            echo "<td>" .$rowItens["id"]. "</td>
                            <td>" .$rowItens["name"]. "</td>
                            <td>" .$rowItens["state"]. "</td>";

                            if($rowItens["state"] == "active"){ //Se o estado do item esta ativo
                                echo "<td>[editar][desativar][apagar]</td>";
                            }
                            else{ //Se o estado do item esta inativo
                                echo "<td>[editar][ativar][apagar]</td>";
                            }

                            echo "</td></tr>";
                        }
                        }
                        else{ //Se não exitem itens para o tipo de item em questão
                            echo "<tr><td>" .$rowTipos["name"]. "</td>
                            <td colspan = 4>Não existem itens para este tipo de item</td>
                            </tr>";
                        }

                    }

            echo "</table>";
        }
        else{ //Se não existe itens
            echo "\n <h1>Não há itens</h1>";
        }

        echo "\n <h3>Gestão de itens - introdução</h3>";

        echo "
        <body>
        <form action='' method='POST'>
        <label>Nome:<input type='text' name='item_nome' required><br>";

        $queryTipos2 = "SELECT item_type.id, item_type.name 
        FROM item_type"; //Query dos tipos de itens
        $resultTipos2 = mysqli_query($link, $queryTipos2);

        echo "<br><label>Tipo:<br>";

        while($rowTipos2=mysqli_fetch_assoc($resultTipos2)){
        echo "
        <input type='radio' name='item_tipo' value='" .$rowTipos2['id']. "'>
        <label>" .$rowTipos2['name']. "</label><br>";
        }

        echo "
        <br><label>Estado:<br>
        <input type='radio' name='item_estado' value='active'>
        <label>Ativo</label>
        <input type='radio' name='item_estado' value='inactive'>
        <label>Inativo</label>
        <br>
        <input type='hidden' name='estado' value='inserir'>
        <br><input type='submit' value='Inserir Item'>
        <br><br>
        </form>
        </body>";

        button_voltar();
    }
    elseif(isset($_REQUEST["estado"])=="inserir"){

        echo "\n <h3>Gestão de itens - inserção</h3>";

        $itemInserir=trim($_POST["item_nome"]);
        $erro = false;

        if(empty($itemInserir) && $erro == false){ //Se não escreveu um nome
            echo "\n <h1 style='color:Red;'>Valor tem de ter nome</h1>";
            $erro = true;
        }
        else if(preg_match('/[0-9]/',$itemInserir) && $erro == false){ //Se o nome do item tem um número
            echo "\n <h1 style='color:Red;'>Nome do valor não aceita números</h1>";
            $erro = true;
        }
        else{

            $queryVerificaItem = "SELECT * FROM item"; //Query da tabela subitem_allowed_value
            $resultVerificaItem = mysqli_query($link, $queryVerificaItem);

            while($rowVerificaItem = mysqli_fetch_assoc($resultVerificaItem)){
                if(strcmp($rowVerificaItem["name"],$itemInserir)==0 && $erro==false){ //Se o nome do item já exite na base de dados
                    echo "\n <h1 style='color:Red;'>Já existe um item com este nome na base de dados</h1>";
                    $erro = true;
                }
            }
        }

        if($erro == false){

            $queryInsereItens = "INSERT INTO item(id,name,item_type_id,state) 
            VALUES (NULL,'$itemInserir',
            '".$_POST['item_tipo']."',
            '".$_POST['item_estado']."')";
            $resultInsereItens = mysqli_query($link,$queryInsereItens);

            if($resultInsereItens){
                echo "<p style='color:green;'>Inseriu os dados de novo item com sucesso</p>
                <p>Clique em <a href='$current_page'>Continuar</a> para avançar<br>";
            }
            else{
                echo "<p style='color:red;'>Ocorreu um erro ao acessar os dados</p>";
            }
        }

        button_voltar();

    }

}
else{
    echo "\n <h1>Não tem autorização para aceder a esta página</h1>";
}

?>