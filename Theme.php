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
        
        $this->enqueueScript('app', 'endereco', 'js/endereco.js');
        $this->enqueueScript('app', 'num-sniic', 'js/num-sniic.js');
        
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
                
                'esfera' => [
                    'label' => 'Esfera',
                    'type' => 'select',
                    'options' => [
                        'Pública' => 'Pública',
                        'Privada' => 'Privada'
                    ]
                ],
                
                'esfera_tipo' => [
                    'label' => 'Tipo de esfera',
                    'type' => 'select',
                    'options' => [
                        'Federal'           => 'Federal',
                        'Estadual'          => 'Estadual',
                        'Municipal'         => 'Municipal',
                        'Associação'        => 'Associação',
                        'Empresa'           => 'Empresa',
                        'Fundação'          => 'Fundação',
                        'Particular'        => 'Particular',
                        'Religiosa'         => 'Religiosa',
                        'Mista'             => 'Mista',
                        'Entidade Sindical' => 'Entidade Sindical',
                        'Outra'             => 'Outra',
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

                'En_CEP' => [
                    'label' => 'CEP',
                ],
                'En_Nome_Logradouro' => [
                    'label' => 'Logradouro',
                ],
                'En_Num' => [
                    'label' => 'Número',
                ],
                'En_Complemento' => [
                    'label' => 'Complemento',
                ],
                'En_Bairro' => [
                    'label' => 'Bairro',
                ],
                'En_Municipio' => [
                    'label' => 'Município',
                ],
                'En_Estado' => [
                    'label' => 'Estado',
                    'type' => 'select',
                    'options' => array(
                        'AC'=>'Acre',
                        'AL'=>'Alagoas',
                        'AP'=>'Amapá',
                        'AM'=>'Amazonas',
                        'BA'=>'Bahia',
                        'CE'=>'Ceará',
                        'DF'=>'Distrito Federal',
                        'ES'=>'Espírito Santo',
                        'GO'=>'Goiás',
                        'MA'=>'Maranhão',
                        'MT'=>'Mato Grosso',
                        'MS'=>'Mato Grosso do Sul',
                        'MG'=>'Minas Gerais',
                        'PA'=>'Pará',
                        'PB'=>'Paraíba',
                        'PR'=>'Paraná',
                        'PE'=>'Pernambuco',
                        'PI'=>'Piauí',
                        'RJ'=>'Rio de Janeiro',
                        'RN'=>'Rio Grande do Norte',
                        'RS'=>'Rio Grande do Sul',
                        'RO'=>'Rondônia',
                        'RR'=>'Roraima',
                        'SC'=>'Santa Catarina',
                        'SP'=>'São Paulo',
                        'SE'=>'Sergipe',
                        'TO'=>'Tocantins',
                    )
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
                'En_CEP' => [
                    'label' => 'CEP',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Nome_Logradouro' => [
                    'label' => 'Logradouro',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Num' => [
                    'label' => 'Número',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Complemento' => [
                    'label' => 'Complemento',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Bairro' => [
                    'label' => 'Bairro',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Municipio' => [
                    'label' => 'Município',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                ],
                'En_Estado' => [
                    'label' => 'Estado',
                    'private' => function(){
                        return !$this->publicLocation;
                    },
                    'type' => 'select',

                    'options' => array(
                        'AC'=>'Acre',
                        'AL'=>'Alagoas',
                        'AP'=>'Amapá',
                        'AM'=>'Amazonas',
                        'BA'=>'Bahia',
                        'CE'=>'Ceará',
                        'DF'=>'Distrito Federal',
                        'ES'=>'Espírito Santo',
                        'GO'=>'Goiás',
                        'MA'=>'Maranhão',
                        'MT'=>'Mato Grosso',
                        'MS'=>'Mato Grosso do Sul',
                        'MG'=>'Minas Gerais',
                        'PA'=>'Pará',
                        'PB'=>'Paraíba',
                        'PR'=>'Paraná',
                        'PE'=>'Pernambuco',
                        'PI'=>'Piauí',
                        'RJ'=>'Rio de Janeiro',
                        'RN'=>'Rio Grande do Norte',
                        'RS'=>'Rio Grande do Sul',
                        'RO'=>'Rondônia',
                        'RR'=>'Roraima',
                        'SC'=>'Santa Catarina',
                        'SP'=>'São Paulo',
                        'SE'=>'Sergipe',
                        'TO'=>'Tocantins',
                    )
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
}