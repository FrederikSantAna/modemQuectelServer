<html>
    <body>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="size" />
            <input type="file" name="file" />
            <input type="submit" name="acao" value="Enviar" />
        </form>            
        <?php
            if(isset($_POST['acao']) && isset($_POST['size']))
            {
                $arquivo = $_FILES['file']; // arquivo enviado no upload
                $extensao = explode('.',$arquivo['name']); 
                
                if($extensao[sizeof($extensao)-1] != 'hex')
                {
                    die('Somente arquivo HEX!');
                }
                else
                {
                    //echo 'Upload feito com sucesso!';

                    // exclui arquivos atuais
                    $pasta = "uploads/";
                    $diretorio = dir($pasta);
                    while(($arquivoPasta = $diretorio->read()) !== false) 
                    {
                        @unlink($pasta.$arquivoPasta);
                    }
                    $diretorio->close();
                    
                    // substitui por novo arquivo
                    move_uploaded_file($arquivo['tmp_name'],'uploads/'.$arquivo['name']);

                    // divide o arquivo em blocos
                    $arquivoPasta = file_get_contents($pasta.$arquivo['name']); // abre arquivo como string
                    $rd = 0;
                    $bloco = (int)$_POST['size']; // tamanho dos blocos
                    $tamanhoArquivo = strlen($arquivoPasta);
                    if($bloco == 0)
                    {
                        $quantidadeBlocos = 0;
                    }
                    else
                    {
                        $quantidadeBlocos = ceil($tamanhoArquivo/$bloco);
                        
                        for($i = 1; $i <= $quantidadeBlocos; $i++)
                        {
                            $parte = fopen($pasta.$i.'.hex','w'); // criar arquivo para escrita
    
                            $arquivoPastaSubStr = substr($arquivoPasta, $rd, $bloco);
                            fwrite($parte, $arquivoPastaSubStr);
                            
                            $rd += $bloco;
                        }
    
                        // apaga o arquivo com tamanho completo
                        @unlink($pasta.$arquivo['name']);
    
                        // cria primeiro bloco com informacoes da quantidade de blocos
                        $parte = fopen($pasta.$arquivo['name'],'w'); // criar arquivo para escrita
                        $primeiroBloco = "Blocos=".$quantidadeBlocos;
                        fwrite($parte, $primeiroBloco);                        
                    }

                    $diretorio = dir($pasta);
                    
                    echo "Tamanho do blocos='<strong>".$bloco."</strong>':<br />";
                    echo "Lista de Arquivos do diretório '<strong>".$pasta."</strong>':<br />";
                    while($arquivo = $diretorio -> read())
                    {
                        $item[$arquivo] =  $arquivo; 
                        echo "<a href='".$pasta.$arquivo."'>".$arquivo."</a><br />";
                    }
                    $diretorio -> close();                    
                }
            }
        ?>
    </body>
</html>