<?php 

require_once("custom/php/common.php");

if(!$link){
    echo "Connection error";
}
else{
    //echo "Connection succeded";
}

//Não sei fazer variáveis de sessão

if(is_user_logged_in() && current_user_can("manage_allowed_values")){
    if(!isset($_REQUEST["estado"])){

        echo "<table>
            <tr>
                <th>Item</th>
                <td>ID</td>
                <th>Subitem</th>
                <td>ID</td>
                <td>Valores permitidos</td>
                <td>Estado</td>
                <td>Ação</td>
            </tr>";

            $queryTabelaSubitem = "SELECT * FROM subitem WHERE subitem.value_type = 'enum'"; //Query da tabela subitem para verificar que existem subitens com "value_type" igual a "enum"
            $resultTabelaSubitem = mysqli_query($link, $queryTabelaSubitem);

            if(mysqli_num_rows($resultTabelaSubitem) > 0){ //Se existem subitens com "value_type" igual a "enum"

                $queryItens = "SELECT item.id, item.name FROM item"; //Query de todos os itens
                $resultItens = mysqli_query($link,$queryItens);
                while($rowItens = mysqli_fetch_assoc($resultItens)){

                    $queryRows = "SELECT * 
                    FROM subitem, item, subitem_allowed_value 
                    WHERE item.id = subitem.item_id AND subitem.item_id = " .$rowItens["id"]. " AND subitem.id = subitem_allowed_value.subitem_id AND subitem.value_type = 'enum'"; //Query para saber quantas rows a divisão de item em questão terá de ter (Query de todos os subitens que estão na tabela subitem_allowed_value que têm "value_type" igual a "enum")
                    $resultRows = mysqli_query($link, $queryRows);

                    $queryRows2 = "SELECT * 
                    FROM subitem, item 
                    WHERE item.id = subitem.item_id AND subitem.item_id = " .$rowItens["id"]. " AND subitem.value_type = 'enum' AND subitem.id NOT IN (SELECT subitem_allowed_value.subitem_id FROM subitem_allowed_value)"; //Query para saber quantas rows a divisão de item em questão terá de ter (Query de todos os subitens que não estão na tabela subitem_allowed_value que têm "value_type" igual a "enum")
                    $resultRows2 = mysqli_query($link, $queryRows2);

                    $querySubitens = "SELECT subitem.name, subitem.id 
                    FROM subitem, item 
                    WHERE item.id = subitem.item_id AND subitem.value_type = 'enum' AND subitem.item_id = " .$rowItens["id"]. " 
                    ORDER BY subitem.name"; //Query de todos os subitens do item em questão que têm "value_type" igual a "enum", ordenados alfabéticamente
                    $resultSubitens = mysqli_query($link, $querySubitens);

                    if(mysqli_num_rows($resultSubitens) > 0 && mysqli_num_rows($resultRows) > 0){ //Se o item tem subitens e se os seus subitens tem valores "allowed"

                        echo "<tr><td rowspan=" .mysqli_num_rows($resultRows) + mysqli_num_rows($resultRows2). ">" .$rowItens["name"]. "</td>"; //Tamanho da divisão é a soma o numero de linhas dos subitens que são "allowed" e que não são "allowed" pois ambos terão de ser apresentados

                        while($rowSubitens = mysqli_fetch_assoc($resultSubitens)){

                            $queryPermitidos = "SELECT subitem_allowed_value.id, subitem_allowed_value.value, subitem_allowed_value.state 
                            FROM subitem_allowed_value, subitem 
                            WHERE subitem.id = " .$rowSubitens["id"]. " AND subitem_allowed_value.subitem_id = subitem.id"; // Querry dos dos valores "allowed" do subitem em questão
                            $resultPermitidos = mysqli_query($link, $queryPermitidos);

                            $pagina = "?estado=introducao&subitem=" .$rowSubitens["id"]."";

                            if(mysqli_num_rows($resultPermitidos) > 0){ //Se o subitem tiver valores "allowed"

                                echo "<td rowspan=" .mysqli_num_rows($resultPermitidos). ">" .$rowSubitens["id"]. "</td>
                                <td rowspan=" .mysqli_num_rows($resultPermitidos). ">[<a href='$current_page.$pagina'>" .$rowSubitens["name"]. "</a>]</td>";

                                while($rowPermitidos = mysqli_fetch_assoc($resultPermitidos)){

                                    echo "<td>" .$rowPermitidos["id"]. "</td>
                                    <td>" .$rowPermitidos["value"]. "</td>
                                    <td>" .$rowPermitidos["state"]. "</td>";

                                    if($rowPermitidos["state"] == "active"){ //Se o estado do valor está ativo
                                        echo "<td>[editar]<br>[desativar]<br>[apagar]</td>";
                                    }
                                    else{ //Se o estado do valor está inativo
                                        echo "<td>[editar]<br>[ativar]<br>[apagar]</td>";
                                    }

                                    echo "</tr>";

                                }
                            }
                            else{ //Se o subitem não tiver valores "allowed"

                                echo "<td>" .$rowSubitens["id"]. "</td>
                                <td>[<a href='$current_page.$pagina'>" .$rowSubitens["name"]. "</a>]</td>
                                <td colspan = 4>Não há valores permitidos definidos</td>
                                </tr>";

                            }
                        }
                    }
                    else if (mysqli_num_rows($resultSubitens) > 0 && mysqli_num_rows($resultRows) == 0){ //Se o item tem subitens mas os subitens não têm valores "allowed"

                        echo "<td rowspan=" .mysqli_num_rows($resultSubitens). ">" .$rowItens["name"]. "</td>";

                        while($rowSubitens = mysqli_fetch_assoc($resultSubitens)){

                            $pagina = "?estado=introducao&subitem=" .$rowSubitens["id"]."";

                            echo "<td>" .$rowSubitens["id"]. "</td>
                            <td>[<a href='$current_page.$pagina'>" .$rowSubitens["name"]. "</a>]</td>
                            <td colspan = 4>Não há valores permitidos definidos</td>
                            </tr>";
                        }
                }
            }
        }
        else{ //Se não existem subitens com "value_type" igual a "enum"
            echo "\n <h1>Não há subitems especificados cujo tipo de valor seja enum. \n Especificar primeiro novo(s) item(s) e depois voltar a esta opção.</h1>";
        }

        echo "</table>";

        button_voltar();
    }
    elseif(isset($_REQUEST["estado"])=="introducao"){

        echo "\n <h3>Gestão de valores permitidos - introdução</h3>";

        //$_SESSION["subitem_id"] = $_REQUEST["subitem"];

        echo "
        <body>
        <form action='' method='POST'>
        <label>Valor:<input type='text' name='vp_nome' required><br>
        <input type='hidden' name='estado' value='inserir'><br>
        <input type='submit' value='Inserir valor permitido'>
        </form>
        </body><br>";

        button_voltar();

    }
    elseif(isset($_REQUEST["estado"])=="inserir"){

        echo "\n <h3>Gestão de valores permitidos - inserção</h3>";

        $valorInserir=trim($_POST["vp_nome"]);
        $erro = false;

        if(empty($valorInserir) && $erro == false){ //Se não escreveu um nome
            echo "\n <h1 style='color:Red;'>Valor tem de ter nome</h1>";
            $erro = true;
        }
        else if(preg_match('/[0-9]/',$valorInserir) && $erro == false){ //Se o nome do valor tem um número
            echo "\n <h1 style='color:Red;'>Nome do valor não aceita números</h1>";
            $erro = true;
        }
        else{

            $queryVerificaValor = "SELECT * FROM subitem_allowed_value"; //Query da tabela subitem_allowed_value
            $resultVerificaValor = mysqli_query($link, $queryVerificaValor);

            while($rowVerificaValor = mysqli_fetch_assoc($resultVerificaValor)){
                if(strcmp($rowVerificaValor["value"],$valorInserir)==0 && $erro==false){ //Se o nome do valor já exite na base de dados
                    echo "\n <h1 style='color:Red;'>Já existe um valor com este nome na base de dados</h1>";
                    $erro = true;
                }
            }
        }

        if($erro == false){

            $queryInsereValor = "INSERT INTO subitem_allowed_value(id,subitem_id,value,state) 
            VALUES (NULL,
            '".$_SESSION['subitem_id']."',
            '$valorInserir',
            'active')";
            $resultInsereValor= mysqli_query($link,$queryInsereValor);

            if($resultInsereValor){
                echo "<p style='color:green;'>Inseriu os dados de novo item com sucesso</p>
                <p>Clique em <a href='$current_page'>Continuar</a> para avançar<br>";
            }
            else{
                echo "<p style='color:red;'>Ocorreu um erro ao acessar os dados</p>";
            }
        }

    }
}
else{
    echo "\n <h1>Não tem autorização para aceder a esta página</h1>";
}

?>