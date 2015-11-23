<?php
$action = preg_replace("#^(\w+/)#", "", $this->template);
$this->bodyProperties['ng-app'] = "Entity";

$this->addEntityToJs($entity);

if($this->isEditable()){
    $this->addEntityTypesToJs($entity);
    $this->addTaxonoyTermsToJs('area');

    $this->addTaxonoyTermsToJs('tag');
}
$this->includeMapAssets();

$this->includeAngularEntityAssets($entity);

?>
<?php $this->part('editable-entity', array('entity'=>$entity, 'action'=>$action));  ?>

<article class="main-content agent">
    <header class="main-content-header">
        <div
            <?php if ($header = $entity->getFile('header')): ?>
                style="background-image: url(<?php echo $header->transform('header')->url; ?>);" class="header-image js-imagem-do-header"
            <?php elseif($this->isEditable()): ?>
                class="header-image js-imagem-do-header"
            <?php endif; ?>
            >
            <?php if ($this->isEditable()): ?>
                <a class="btn btn-default edit js-open-editbox" data-target="#editbox-change-header" href="#">Editar</a>
                <div id="editbox-change-header" class="js-editbox mc-bottom" title="Editar Imagem da Capa">
                    <?php $this->ajaxUploader($entity, 'header', 'background-image', '.js-imagem-do-header', '', 'header'); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php $this->part('entity-status', array('entity' => $entity)); ?>
        <!--.header-image-->
        <div class="header-content">
            <?php if($avatar = $entity->avatar): ?>
            <div class="avatar com-imagem">
                    <img src="<?php echo $avatar->transform('avatarBig')->url; ?>" alt="" class="js-avatar-img" />
                <?php else: ?>
                    <div class="avatar">
                        <img class="js-avatar-img" src="<?php $this->asset('img/avatar--agent.png'); ?>" />
            <?php endif; ?>
                <?php if($this->isEditable()): ?>
                    <a class="btn btn-default edit js-open-editbox" data-target="#editbox-change-avatar" href="#">editar</a>
                    <div id="editbox-change-avatar" class="js-editbox mc-right" title="Editar avatar">
                        <?php $this->ajaxUploader ($entity, 'avatar', 'image-src', 'div.avatar img.js-avatar-img', '', 'avatarBig'); ?>
                    </div>
                <?php endif; ?>
                <!-- pro responsivo!!! -->
                <?php if($entity->isVerified): ?>
                    <a class="verified-seal hltip active" title="Este <?php echo $entity->entityType ?> é verificado." href="#"></a>
                <?php endif; ?>
            </div>
            <!--.avatar-->
            <div class="entity-type agent-type">
                <div class="icon icon-agent"></div>
                <a href="#" class='js-editable-type' data-original-title="Tipo" data-emptytext="Selecione um tipo" data-entity='agent' data-value='<?php echo $entity->type ?>'>
                    <?php echo $entity->type->name; ?>
                </a>
            </div>
            <!--.entity-type-->


            <?php if($this->isEditable()): ?>
            <div class="entity-type agent-type" ng-controller="AgentTypesController">
                <div class="icon icon-agent"></div>
                <a class="editable" ng-click="editBox.open('eb-tipologia', $event)">{{data.tipologia3 ? data.tipologia3 : 'Escolha uma tipologia'}}</a>

                <edit-box id="eb-tipologia" position="bottom" cancel-label="Cancelar" submit-label="Enviar" on-submit="setTypes" on-cancel="resetValues" close-on-cancel="1">
                    <input type="hidden" id="tipologia_nivel1" class="js-editable" data-edit="tipologia_nivel1" data-emptytext="">
                    <input type="hidden" id="tipologia_nivel2" class="js-editable" data-edit="tipologia_nivel2" data-emptytext="">
                    <input type="hidden" id="tipologia_nivel3" class="js-editable" data-edit="tipologia_nivel3" data-emptytext="">
                    <label>
                        nível 1:
                        <select ng-model="data._tipo1" ng-change="set(1)">
                            <option ng-repeat="val in data._valores_nivel1" ng-value="val">{{val}}</option>
                        </select>
                    </label>
                    <label ng-show="data._tipo1">
                        nível 2:
                        <select ng-model="data._tipo2"  ng-change="set(2)">
                            <option ng-repeat="val in data._valores_nivel2" ng-value="val">{{val}}</option>
                        </select>
                    </label>
                    <label ng-show="data._tipo2">
                        nível 3:
                        <select ng-model="data._tipo3">
                            <option ng-repeat="val in data._valores_nivel3" ng-value="val">{{val}}</option>
                        </select>
                    </label>
                </edit-box>
            </div>
            <!--.entity-type-->
            <?php else: ?>
                <div class="entity-type agent-type">
                    <div class="icon icon-agent"></div>
                    <a href="#"><?php echo $entity->tipologia_nivel3 ?></a>
                </div>
            <?php endif; ?>
            <h2><span class="js-editable" data-edit="name" data-original-title="Nome de exibição" data-emptytext="Nome de exibição"><?php echo $entity->name; ?></span></h2>
            <p class="num_sniic"><span class="label">Nº SNIIC:</span> <span id="num-sniic"><?php echo $entity->num_sniic ? $entity->num_sniic : "Preencha os campos obrigatorios e clique em salvar para gerar"; ?></span></p>
        </div>
    </header>
    <ul class="abas clearfix clear">
        <li class="active"><a href="#sobre">Sobre</a></li>
        <li><a href="#agenda">Agenda</a></li>
    </ul>
    <div id="sobre" class="aba-content">
        <div class="ficha-spcultura">
            <?php if($this->isEditable() && $entity->shortDescription && strlen($entity->shortDescription) > 400): ?>
                <div class="alert warning">O limite de caracteres da descrição curta foi diminuido para 400, mas seu texto atual possui <?php echo strlen($entity->shortDescription) ?> caracteres. Você deve alterar seu texto ou este será cortado ao salvar.</div>
            <?php endif; ?>

            <p>
                <span class="js-editable" data-edit="shortDescription" data-original-title="Descrição Curta" data-emptytext="Insira uma descrição curta" data-showButtons="bottom" data-tpl='<textarea maxlength="400"></textarea>'><?php echo $this->isEditable() ? $entity->shortDescription : nl2br($entity->shortDescription); ?></span>
            </p>
            <div class="servico">

                <?php if($this->isEditable() || $entity->site): ?>
                    <p><span class="label">Site:</span>
                    <?php if($this->isEditable()): ?>
                        <span class="js-editable" data-edit="site" data-original-title="Site" data-emptytext="Insira a url de seu site"><?php echo $entity->site; ?></span></p>
                    <?php else: ?>
                        <a class="url" href="<?php echo $entity->site; ?>"><?php echo $entity->site; ?></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if($this->isEditable()): ?>
                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">Nome:</span> <span class="js-editable" data-edit="nomeCompleto" data-original-title="Nome Completo ou Razão Social" data-emptytext="Insira seu nome completo ou razão social"><?php echo $entity->nomeCompleto; ?></span></p>
                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">CPF/CNPJ:</span> <span class="js-editable" data-edit="documento" data-original-title="CPF/CNPJ" data-emptytext="Insira o CPF ou CNPJ com pontos, hífens e barras"><?php echo $entity->documento; ?></span></p>
                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">Data de Nascimento/Fundação:</span>
                        <span class="js-editable" data-type="date" data-edit="dataDeNascimento" data-viewformat="dd/mm/yyyy" data-showbuttons="false" data-original-title="Data de Nascimento/Fundação" data-emptytext="Insira a data de nascimento ou fundação do agente">
                            <?php $dtN = (new DateTime)->createFromFormat('Y-m-d', $entity->dataDeNascimento); echo $dtN ? $dtN->format('d/m/Y') : ''; ?>
                        </span>
                    </p>
                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">Gênero:</span> <span class="js-editable" data-edit="genero" data-original-title="Gênero" data-emptytext="Selecione o gênero se for pessoa física"><?php echo $entity->genero; ?></span></p>
                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">Raça/Cor:</span> <span class="js-editable" data-edit="raca" data-original-title="Raça/cor" data-emptytext="Selecione a raça/cor se for pessoa física"><?php echo $entity->raca; ?></span></p>

                    <p class="privado"><span class="icon icon-private-info"></span><span class="label">Email Privado:</span> <span class="js-editable" data-edit="emailPrivado" data-original-title="Email Privado" data-emptytext="Insira um email que não será exibido publicamente"><?php echo $entity->emailPrivado; ?></span></p>
                <?php endif; ?>

                <?php if($this->isEditable() || $entity->emailPublico): ?>
                <p><span class="label">Email:</span> <span class="js-editable" data-edit="emailPublico" data-original-title="Email Público" data-emptytext="Insira um email que será exibido publicamente"><?php echo $entity->emailPublico; ?></span></p>
                <?php endif; ?>

                <?php if($this->isEditable() || $entity->telefonePublico): ?>
                <p><span class="label">Telefone Público:</span> <span class="js-editable js-mask-phone" data-edit="telefonePublico" data-original-title="Telefone Público" data-emptytext="Insira um telefone que será exibido publicamente"><?php echo $entity->telefonePublico; ?></span></p>
                <?php endif; ?>

                <?php if($this->isEditable()): ?>
                <p class="privado"><span class="icon icon-private-info"></span><span class="label">Telefone 1:</span> <span class="js-editable js-mask-phone" data-edit="telefone1" data-original-title="Telefone Privado" data-emptytext="Insira um telefone que não será exibido publicamente"><?php echo $entity->telefone1; ?></span></p>
                <p class="privado"><span class="icon icon-private-info"></span><span class="label">Telefone 2:</span> <span class="js-editable js-mask-phone" data-edit="telefone2" data-original-title="Telefone Privado" data-emptytext="Insira um telefone que não será exibido publicamente"><?php echo $entity->telefone2; ?></span></p>
                <?php endif; ?>
            </div>

            <?php $lat = $entity->location->latitude; $lng = $entity->location->longitude; ?>
            <?php if ( $this->isEditable() || ($entity->publicLocation && $lat && $lng) ): ?>
                <div class="servico clearfix">
                    <div class="mapa js-map-container">
                        <?php if($this->isEditable()): ?>
                            <div class="clearfix js-leaflet-control" data-leaflet-target=".leaflet-top.leaflet-left">
                                <a id ="button-locate-me" class="control-infobox-open hltip botoes-do-mapa" title="Encontrar minha localização"></a>
                            </div>
                        <?php endif; ?>
                        <div id="single-map-container" class="js-map" data-lat="<?php echo $lat?>" data-lng="<?php echo $lng?>"></div>
                        <input type="hidden" id="map-target" data-name="location" class="js-editable" data-edit="location" data-value="<?php echo '[' . $lng . ',' . $lat . ']'; ?>"/>
                    </div>
                    <!--.mapa-->
                    <div class="infos">
                        <input type="hidden" class="js-editable" id="endereco" data-edit="endereco" data-original-title="Endereço" data-emptytext="Insira o endereço" data-showButtons="bottom" value="<?php echo $entity->endereco ?>" data-value="<?php echo $entity->endereco ?>">
                        <p class="endereco"><span class="label">Endereço:</span> <span class="js-endereco"><?php echo $entity->endereco ?></span></p>
                        <p><span class="label">CEP:</span> <span class="js-editable" id="En_CEP" data-edit="En_CEP" data-original-title="CEP" data-emptytext="Insira o CEP" data-showButtons="bottom"><?php echo $entity->En_CEP ?></span></p>
                        <p><span class="label">Logradouro:</span> <span class="js-editable" id="En_Nome_Logradouro" data-edit="En_Nome_Logradouro" data-original-title="Logradouro" data-emptytext="Insira o logradouro" data-showButtons="bottom"><?php echo $entity->En_Nome_Logradouro ?></span></p>
                        <p><span class="label">Número:</span> <span class="js-editable" id="En_Num" data-edit="En_Num" data-original-title="Número" data-emptytext="Insira o Número" data-showButtons="bottom"><?php echo $entity->En_Num ?></span></p>
                        <p><span class="label">Complemento:</span> <span class="js-editable" id="En_Complemento" data-edit="En_Complemento" data-original-title="Complemento" data-emptytext="Insira um complemento" data-showButtons="bottom"><?php echo $entity->En_Complemento ?></span></p>
                        <p><span class="label">Bairro:</span> <span class="js-editable" id="En_Bairro" data-edit="En_Bairro" data-original-title="Bairro" data-emptytext="Insira o Bairro" data-showButtons="bottom"><?php echo $entity->En_Bairro ?></span></p>
                        <p><span class="label">Município:</span> <span class="js-editable" id="En_Municipio" data-edit="En_Municipio" data-original-title="Município" data-emptytext="Insira o Município" data-showButtons="bottom"><?php echo $entity->En_Municipio ?></span></p>
                        <p><span class="label">Estado:</span> <span class="js-editable" id="En_Estado" data-edit="En_Estado" data-original-title="Estado" data-emptytext="Insira o Estado" data-showButtons="bottom"><?php echo $entity->En_Estado ?></span></p>
                        <?php if($this->isEditable()): ?>
                            <p class="privado">
                                <span class="icon icon-private-info"></span><span class="label">Localização:</span>
                                <span class="js-editable clear" data-edit="publicLocation" data-type="select" data-showbuttons="false"
                                    data-value="<?php echo $entity->publicLocation ? '1' : '0';?>"
                                    data-source="[{value: 1, text: 'Pública'},{value: 0, text:'Privada'}]">
                                </span>
                            </p>
                        <?php endif; ?>

                        <?php foreach($app->getRegisteredGeoDivisions() as $geo_division): $metakey = $geo_division->metakey; ?>
                            <p <?php if(!$entity->$metakey) { echo 'style="display:none"'; }?>>
                                <span class="label"><?php echo $geo_division->name ?>:</span> <span class="js-geo-division-address" data-metakey="<?php echo $metakey ?>"><?php echo $entity->$metakey; ?></span>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    <!--.infos-->
                </div>
                <!--.servico-->
            <?php endif; ?>

        </div>
        <!--.ficha-spcultura-->

        <?php if ( $this->isEditable() || $entity->longDescription ): ?>
            <h3>Descrição</h3>
            <span class="descricao js-editable" data-edit="longDescription" data-original-title="Descrição do Agente" data-emptytext="Insira uma descrição do agente" ><?php echo $this->isEditable() ? $entity->longDescription : nl2br($entity->longDescription); ?></span>
        <?php endif; ?>
        <!--.descricao-->
        <!-- Video Gallery BEGIN -->
            <?php $this->part('video-gallery.php', array('entity'=>$entity)); ?>
        <!-- Video Gallery END -->
        <!-- Image Gallery BEGIN -->
            <?php $this->part('gallery.php', array('entity'=>$entity)); ?>
        <!-- Image Gallery END -->
    </div>
    <!-- #sobre -->
    <div id="agenda" class="aba-content">
        <?php $this->part('agenda', array('entity' => $entity)); ?>
    </div>
    <!-- #agenda -->
    <?php $this->part('owner', array('entity' => $entity, 'owner' => $entity->owner)); ?>
</article>
<div class="sidebar-left sidebar agent">
    <?php $this->part('verified', array('entity' => $entity)); ?>
    <?php $this->part('widget-areas', array('entity'=>$entity)); ?>
    <?php $this->part('widget-tags', array('entity'=>$entity)); ?>
    <?php $this->part('redes-sociais', array('entity'=>$entity)); ?>
</div>
<div class="sidebar agent sidebar-right">
    <?php if($this->controller->action == 'create'): ?>
        <div class="widget">
            <p class="alert info">Para adicionar arquivos para download ou links, primeiro é preciso salvar o agente.<span class="close"></span></p>
        </div>
    <?php endif; ?>

    <!-- Related Agents BEGIN -->
        <?php $this->part('related-agents.php', array('entity'=>$entity)); ?>
    <!-- Related Agents END -->

    <?php if(count($entity->spaces) > 0): ?>
    <div class="widget">
        <h3>Espaços do agente</h3>
        <ul class="widget-list js-slimScroll">
            <?php foreach($entity->spaces as $space): ?>
            <li class="widget-list-item"><a href="<?php echo $app->createUrl('space', 'single', array('id' => $space->id)) ?>"><span><?php echo $space->name; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <!--
    <div class="widget">
        <h3>Projetos do agente</h3>
        <ul>
            <li><a href="#">Projeto 1</a></li>
            <li><a href="#">Projeto 2</a></li>
            <li><a href="#">Projeto 3</a></li>
        </ul>
    </div>
    -->

    <!-- Downloads BEGIN -->
        <?php $this->part('downloads.php', array('entity'=>$entity)); ?>
    <!-- Downloads END -->

    <!-- Link List BEGIN -->
        <?php $this->part('link-list.php', array('entity'=>$entity)); ?>
    <!-- Link List END -->
</div>
