<?php
namespace BaseMinc;
use MapasCulturais\Themes\BaseV1;
use MapasCulturais\App;

abstract class Theme extends BaseV1\Theme{
    abstract function getMetadataPrefix();
    
    abstract protected function _getAgentMetadata();
    abstract protected function _getSpaceMetadata();
    abstract protected function _getEventMetadata();
    abstract protected function _getProjectMetadata();
    
    static function getThemeFolder() {
        return __DIR__;
    }
    
    protected function _init() {
        parent::_init();
        $app = App::i();
        
        $app->hook('entity(<<Agent|Space|Event|Project>>).save:after', function() use ($app){
            if(!$this->getValidationErrors()){
                $num = strtoupper(substr($this->entityType, 0, 2)) . '-' . $this->id;
                $this->num_sniic = $num;
            }
        });
        
        $app->hook('view.render(<<*>>):before', function() use($app) {
            $this->jsObject['angularAppDependencies'][] = 'entity.controller.agentTypes';
        });
        
        $app->hook('template(<<space|agent|project|event>>.<<create|edit|single>>.name):after', function(){
            $this->enqueueScript('app', 'num-sniic', 'js/num-sniic.js');
            $this->part('num-sniic', ['entity' => $this->data->entity]);
        });

        $app->hook('template(agent.<<create|edit|single>>.type):after', function(){
            $this->part('tipologia-agente', ['entity' => $this->data->entity]);
        });

        $app->hook('template(space.<<create|edit|single>>.tab-about-service):before', function(){
            $this->part('mais-campos', ['entity' => $this->data->entity]);
        });
        
        // BUSCA POR NÚMERO SNIIC
        // adiciona o join do metadado
        $app->hook('repo(<<*>>).getIdsByKeywordDQL.join', function(&$joins, $keyword) {
            $joins .= "
                LEFT JOIN 
                        e.__metadata num_sniic 
                WITH 
                        num_sniic.key = 'num_sniic'";
        });

        // filtra pelo valor do keyword
        $app->hook('repo(<<*>>).getIdsByKeywordDQL.where', function(&$where, $keyword) {
            $where .= "OR lower(num_sniic.value) LIKE lower(:keyword)";
        });
        
        // BUSCA POR NÚMERO MUNICIPIO
        // adiciona o join do metadado
        $app->hook('repo(<<*>>).getIdsByKeywordDQL.join', function(&$joins, $keyword) {
            $joins .= "
                LEFT JOIN 
                        e.__metadata En_Municipio 
                WITH 
                        En_Municipio.key = 'En_Municipio'";
        });

        // filtra pelo valor do keyword
        $app->hook('repo(<<*>>).getIdsByKeywordDQL.where', function(&$where, $keyword) {
            $where .= "OR lower(En_Municipio.value) LIKE lower(:keyword)";
        });
        
    }
    
    public function includeAngularEntityAssets($entity) {
        parent::includeAngularEntityAssets($entity);
        
        $this->enqueueScript('app', 'entity.controller.agentType', 'js/ng.entity.controller.agentTypes.js', ['entity.app']);
    }

    public function includeOpeningTimeAssets(){
        $this->jsObject['templateUrl']['spaceOpeningTime'] = $this->asset('js/directives/openingTime.html', false);
        $this->jsObject['angularAppDependencies'][] = 'entity.directive.openingTime';
        $this->enqueueScript('app', 'entity.directive.openingTime', 'js/ng.entity.directive.openingTime.js', array('ng-mapasculturais'));
    }
    
    public function register() {
        parent::register();

        $app = App::i();

        $metadata = [
            'MapasCulturais\Entities\Event' => [
                'num_sniic' => [
                    'label' => 'Nº SNIIC:',
                    'private' => false
                ],
            ],

            'MapasCulturais\Entities\Project' => [
                'num_sniic' => [
                    'label' => 'Nº SNIIC:',
                    'private' => false
                ],
            ],

            'MapasCulturais\Entities\Space' => [
                'num_sniic' => [
                    'label' => 'Nº SNIIC:',
                    'private' => false
                ],
                
                'cnpj' => [
                    'label' => 'CNPJ',
                    'private' => false,
                    'validations' => [
                        'v::cnpj()' => 'O CNPJ informado é inválido'
                    ]
                ],
                
                'esfera' => [
                    'label' => 'Esfera',
                    'type' => 'select',
                    'options' => [
                        'Pública',
                        'Privada'
                    ]
                ],
                
                'esfera_tipo' => [
                    'label' => 'Tipo de esfera',
                    'type' => 'select',
                    'options' => [
                        'Federal',
                        'Estadual',
                        'Distrital',
                        'Municipal',
                        'Associação',
                        'Empresa',
                        'Fundação',
                        'Particular',
                        'Religiosa',
                        'Mista',
                        'Entidade Sindical',
                        'Outra',
                    ],
                ],
                
                'certificado' => [
                    'label' => 'Títulos e Certificados',
                    'type' => 'select',
                    'options' => [
                        'ONG'   => 'Organização não Governamental (ONG)',
                        'OSCIP' => 'Organização da Sociedade Civil de Interesse Público (OSCIP)',
                        'OS'    => 'Organização Social (OS)',
                        'CEBAS' => 'Certificado de Entidade Beneficente de Assistência Social (CEBAS)',
                        'UPF'   => 'Certificado de Utilidade Pública Federal (UPF)',
                        'UPE'   => 'Certificado de Utilidade Pública Estadual (UPE)',
                        'UPM'   => 'Certificado de Utilidade Pública Municipal (UPM)'
                    ]
                ],
            ],

            'MapasCulturais\Entities\Agent' => [
                'num_sniic' => [
                    'label' => 'Nº SNIIC:',
                    'private' => false
                ],

                'tipologia_nivel1' => [
                    'label' => 'Tipologia Nível 1',
                    'private' => false
                ],
                'tipologia_nivel2' => [
                    'label' => 'Tipologia Nível 2',
                    'private' => false
                ],
                'tipologia_nivel3' => [
                    'label' => 'Tipologia Nível 3',
                    'private' => false,
                    'validations' => [
                        'required' => 'A tipologia deve ser informada.'
                    ]
                ],
            ]
        ];
                    
        $prefix = $this->getMetadataPrefix();
                    
        foreach($this->_getAgentMetadata() as $key => $cfg){
            $key = $prefix . $key;
            
            $metadata['MapasCulturais\Entities\Agent'][$key] = $cfg;
        }
                    
        foreach($this->_getSpaceMetadata() as $key => $cfg){
            $key = $prefix . $key;
            
            $metadata['MapasCulturais\Entities\Space'][$key] = $cfg;
        }
                    
        foreach($this->_getEventMetadata() as $key => $cfg){
            $key = $prefix . $key;
            
            $metadata['MapasCulturais\Entities\Event'][$key] = $cfg;
        }
                    
        foreach($this->_getProjectMetadata() as $key => $cfg){
            $key = $prefix . $key;
            
            $metadata['MapasCulturais\Entities\Project'][$key] = $cfg;
        }

        foreach($metadata as $entity_class => $metas){
            foreach($metas as $key => $cfg){
                $def = new \MapasCulturais\Definitions\Metadata($key, $cfg);
                $app->registerMetadata($def, $entity_class);
            }
        }
    }
    
    protected function _getAdvancedFilters(){
        $estado = [
                    'fieldType' => 'checklist',
                    'isArray' => true,
                    'label' => 'Estado',
                    'placeholder' => 'Filtre por Estado',
                    'filter' => [
                        'param' => 'En_Estado',
                        'value' => 'IN({val})'
                    ],
                    'parseValue' => ['join'],
                    'options' => [
                        ['value' => 'AC', 'label' => 'Acre'],
                        ['value' => 'AL', 'label' => 'Alagoas'],
                        ['value' => 'AP', 'label' => 'Amapá'],
                        ['value' => 'AM', 'label' => 'Amazonas'],
                        ['value' => 'BA', 'label' => 'Bahia'],
                        ['value' => 'CE', 'label' => 'Ceará'],
                        ['value' => 'DF', 'label' => 'Distrito Federal'],
                        ['value' => 'ES', 'label' => 'Espírito Santo'],
                        ['value' => 'GO', 'label' => 'Goiás'],
                        ['value' => 'MA', 'label' => 'Maranhão'],
                        ['value' => 'MT', 'label' => 'Mato Grosso'],
                        ['value' => 'MS', 'label' => 'Mato Grosso do Sul'],
                        ['value' => 'MG', 'label' => 'Minas Gerais'],
                        ['value' => 'PA', 'label' => 'Pará'],
                        ['value' => 'PB', 'label' => 'Paraíba'],
                        ['value' => 'PR', 'label' => 'Paraná'],
                        ['value' => 'PE', 'label' => 'Pernambuco'],
                        ['value' => 'PI', 'label' => 'Piauí'],
                        ['value' => 'RJ', 'label' => 'Rio de Janeiro'],
                        ['value' => 'RN', 'label' => 'Rio Grande do Norte'],
                        ['value' => 'RS', 'label' => 'Rio Grande do Sul'],
                        ['value' => 'RO', 'label' => 'Rondônia'],
                        ['value' => 'RR', 'label' => 'Roraima'],
                        ['value' => 'SC', 'label' => 'Santa Catarina'],
                        ['value' => 'SP', 'label' => 'São Paulo'],
                        ['value' => 'SE', 'label' => 'Sergipe'],
                        ['value' => 'TO', 'label' => 'Tocantins'],
                    ],
                ];
        
        $municipio = [
                    'fieldType' => 'text',
                    'label' => 'Município',
                    'placeholder' => 'Busque por Município',
                    'filter' => [
                        'param' => 'En_Municipio',
                        'value' => 'ILIKE(*{val}*)'
                    ],
                    'parseValue' => []
                ];
        
        return [
            'space' => [$estado, $municipio],
            'agent' => [$estado, $municipio],
            'event' => [],
            'project' => []
        ];
    }

}
