# Magento_CorreiosTracking
##### Modulo para rastreio de pedidos através da webservice dos correios.

# Instalação
1. Baixe os arquivos do repositórios.
2. Extraia os arquivos para uma pasta qualquer.
3. Copie as e cole na pasta raiz do magento fazendo um merge das pasta app e etc.

# Configuração
1. Painel Administrativo > Sistema > Configuração > Aba Lateral CaioFlavio Extensions > Rastreio de Pedidos > Correios
  1.1 Você precisa fornecer login e senha de acesso a webservice dos correios.
  1.2 Algumas configurações padrão para o modulo já estão definidas, mas você pode defini-las de acordo com a sua necessidade.
    ![Painel Administrativo](http://i.imgur.com/3KZGJgn.png "Painel Administrativo")

2. Você precisa substituir sua antiga url de rastreio pela nova.
  ```PHP
    <?php echo Mage::helper('custom_tracking')->getTrackingUrl('correios',  $_order->getId()); ?>
  ```
3. O HTML do seu link de rastreio fica mais ou menos assim (exemplo):
  ```HTML
    <a href="<?php echo Mage::helper('custom_tracking')->getTrackingUrl('correios',  $_order->getId()); ?>">Rastreamento</a>
  ```
4. A Visualização da tabela caso o objeto já esteja registrado na base de dados dos correios vai ficar assim:
  ![Tabela retorno ok](http://i.imgur.com/bOVQsrC.png "Tabela retorno ok")

5. A Tabela caso o objeto ainda não esteja registrado mostra apenas o codigo de rastreio e uma mensagem que pode ser setada no admin:
  ![Tabela retorno ruim](http://i.imgur.com/aotaGLN.png "Tabela retorno ruim")

# Pontos a serem melhoroados
##### As vezes a webservice dos correios demora muito a responder, em uma versão futura do modulo será adicionada uma tabela que salva o ultimo status acessado pelo cliente para funcionar como um fallback caso a webservice não responda.

# Caso precise de ajuda
##### Caso precise de ajuda pode ficar a vontade em abrir uma issue ou me enviar uma mensagem ou enviar um email para caioflavio2@hotmail.com.br explicando seu problema.

  

