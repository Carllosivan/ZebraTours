<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class ServiceController extends Controller
{
    private array $services = [
        'passagens-aereas' => [
            'id' => 'passagens-aereas',
            'title' => 'Passagens Aéreas',
            'tagline' => 'Emissão rápida e tarifas competitivas nacionais e internacionais',
            'icon' => 'plane',
            'description' => 'A Zebra Tours trabalha em parceria direta com as principais companhias aéreas que operam em Moçambique e no espaço internacional (LAM, TAP, Emirates, Qatar Airways, South African Airways, Airlink). Garantimos as melhores opções de rotas, classes executiva e económica, marcação de lugares preferenciais e gestão de alterações ou cancelamentos com assistência personalizada 24 horas por dia.',
            'features' => [
                'Voos domésticos (Maputo, Vilankulos, Pemba, Nampula, Beira, Tete)',
                'Voos regionais para África do Sul, Quénia, Angola e Zimbabwe',
                'Voos internacionais para Europa, Médio Oriente, Ásia e Américas',
                'Gestão corporativa de viagens para empresas e missões oficiais',
                'Assistência prioritária para remarcações e imprevistos',
            ],
            'image' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80',
        ],
        'hoteis-alojamento' => [
            'id' => 'hoteis-alojamento',
            'title' => 'Reserva de Hotéis e Lodges',
            'tagline' => 'Resorts à beira-mar, lodges de safari e hotéis corporativos selecionados',
            'icon' => 'building',
            'description' => 'Disponibilizamos uma criteriosa seleção de resorts de luxo no arquipélago de Bazaruto, lodges exclusivos em santuários de vida selvagem e hotéis executivos nos centros nevrálgicos de Maputo, Matola e províncias. Negociamos tarifas especiais com pequeno-almoço incluído, upgrades de quarto e condições flexíveis para os nossos clientes.',
            'features' => [
                'Lodges 5 estrelas em Bazaruto, Benguerra e Santa Carolina',
                'Tented camps de safari autênticos no Parque da Gorongosa',
                'Hotéis executivos para executivos em viagem de negócios',
                'Chalés privados e villas familiares com piscina privada',
                'Políticas de cancelamento flexíveis',
            ],
            'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        ],
        'viagens-lazer-negocios' => [
            'id' => 'viagens-lazer-negocios',
            'title' => 'Viagens de Lazer e Negócios',
            'tagline' => 'Soluções sob medida para férias em família ou missões corporativas',
            'icon' => 'briefcase',
            'description' => 'Compreendemos as particularidades de cada tipo de viagem. Para executivos e delegações, desenhamos itinerários otimizados com faturas organizadas e suporte corporativo. Para famílias e grupos de amigos, planeamos férias relaxantes com itinerários enriquecedores, pensados ao detalhe para máximo descanso e diversão.',
            'features' => [
                'Gestão integrada de viagens corporativas com faturação centralizada',
                'Pacotes de incentivo e eventos de team-building em locais exclusivos',
                'Férias familiares personalizadas com atividades para todas as idades',
                'Lua de mel romântica com comodidades especiais de boas-vindas',
            ],
            'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80',
        ],
        'escapadinhas' => [
            'id' => 'escapadinhas',
            'title' => 'Escapadinhas de Fim de Semana',
            'tagline' => 'Pausas revigorantes de curta duração à saída de Maputo',
            'icon' => 'sun',
            'description' => 'Precisa de desconectar da rotina urbana? As nossas escapadinhas de fim de semana levam-no às praias de águas mornas da Ponta do Ouro, à Reserva Especial de Maputo, às margens da praia de Macaneta ou às montanhas de Namaacha. Pacotes compactos de 2 a 3 noites com tudo tratado para que só tenha de relaxar.',
            'features' => [
                'Ponta do Ouro: Mergulho, observação de golfinhos e praia',
                'Reserva Especial de Maputo: Safari de elefantes e praias virgens',
                'Bilene: Lagoa de águas calmas e desportos náuticos',
                'Transporte terrestre seguro em viaturas todo-o-terreno 4x4',
            ],
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
        ],
        'assistencia-viajante' => [
            'id' => 'assistencia-viajante',
            'title' => 'Assistência ao Viajante e Seguros',
            'tagline' => 'Acompanhamento dedicado do início ao fim da sua viagem',
            'icon' => 'shield',
            'description' => 'A sua tranquilidade é a nossa prioridade. Prestamos consultoria sobre requisitos de vistos de entrada em Moçambique e no estrangeiro, formalidades sanitárias, transferes terrestres e seguros de viagem internacionais com cobertura médica abrangente e proteção de bagagem.',
            'features' => [
                'Apoio nos procedimentos de visto de turismo e negócios',
                'Seguro de viagem internacional com assistência médica de emergência',
                'Transferes aeroporto-hotel privados com motoristas profissionais',
                'Linha de apoio 24/7 em português e inglês',
            ],
            'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80',
        ],
    ];

    public function index(Request $request): Response
    {
        return $this->render('services/index', [
            'pageTitle'    => 'Serviços de Viagens e Turismo | Zebra Tours Moçambique',
            'metaDescription' => 'Conheça todos os nossos serviços: passagens aéreas, alojamentos, viagens de negócios, escapadinhas de fim de semana e assistência ao viajante.',
            'services'     => $this->services,
        ]);
    }

    public function show(Request $request, array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $service = $this->services[$slug] ?? null;

        if (!$service) {
            return $this->render('errors/404', [
                'pageTitle' => 'Serviço Não Encontrado',
                'path'      => "/servicos/{$slug}",
            ]);
        }

        return $this->render('services/show', [
            'pageTitle'       => "{$service['title']} | Zebra Tours Moçambique",
            'metaDescription' => $service['tagline'],
            'service'         => $service,
            'allServices'     => $this->services,
        ]);
    }
}
