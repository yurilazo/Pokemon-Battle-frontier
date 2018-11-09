<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\CodeBattleMove;
use app\models\Effectiveness;
use app\models\Types;
use app\models\Species;
use app\models\SpeciesTypes;
use app\models\Moves;

class BattleController extends Controller
{
    /*
    Este controlador es para la gestión del combate pokemon.
    Se estima que este controlador conozca cuál pokemon tiene cada usuario al frente, en lugar de recibir esa información de afuera, así aminorar riesgos de errores
    */
    public function actionIndex()
    {
        /*
        Antes de la batalla se deben habilitar 4 códigos de movimiento por pokemon, que deberán ser guardados de lado del cliente y del lado del servidor, luego dentro de la batalla los clientes sólo enviarán esos códigos habilitados al servidor, que serían todas sus posibilidades de movimiento en la batalla, el servidor validará si el movimiento es del pokemon activo en la batalla, pues los pokemones que están dentro de sus pokebolas no pueden hacer movimientos, ni tampoco hacer movimientos con los que no pertenescan al pokemon al momento de entrar a la batalla, ni movimientos que pertenezcan a otro pokemon de la batalla, etc.

        Esta función debería recibir los movimientos de ambos usuarios partes de una batalla, tomando el cambio de pokemon también como un movimiento,
        se estima que esta función trabaje, el id del usuario, coockies, organice la prioridad de movimientos, siendo, "cambiar de pokemon" y "pursuit" los movimientos que puedan tener más prioridad, y los movimientos de cálculo de daño los de última prioridad, para luego llamar a la función 'MoveEffect'
        */

        $model = new CodeBattleMove();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

        } else {

        }

        $effectiveness = $this->MoveEffect();
        return $this->render('battle', ['model' => $model, 'effectiveness' => $effectiveness ]);

    }
    public function MoveEffect()
    {
        /*
        Esta función debe ejecutar qué tipo de efecto tendrá un movimiento, tomando en cuenta qué pokemon recibirá el daño, usando la información del controlador, si algún usuario cambia un pokemon o usa un movimiento que ocasiona el cambio de algún pokemon, dicho efecto se ejecutará en esta función, y registrar el resultado en las variables de este controlador, esta función debe llamar a las funciones q cambien de pokemon cambios de estado, o calculo de efecto, o las combinaciones entre ellas
        */

        //$this->actionChangePokemon();
        return $this->DemageCalculate();
    }

    public function DemageCalculate()
    {
        /*
        el código de movimiento contiene la identidad de un pokemon habilitado, por eso se puede obtener al pokemon habilitado (pokemon que esté dentro del team de la batalla y q no esté derrotado) a partir del código de movimiento proporcionado por el usuario
        $attacking_pokemon = get_active_pokemon_be_code_move();
        $defender_pokemon = get_active_pokemon_defender();
        $move = get_attack_be_code_move();
        $attacking_specie = get_specie_be_id_specie($attacking_pokemon['specie']);
        $defender_specie = get_specie_be_id_specie($defender_pokemon['specie']);
        */

        $attacking_pokemon = [
            'id_specie' => 186,
            'pv' => 'jolly',
            'level' => 100,
            'ivs' => ['hp' => 31,'atk' => 31,'def' => 31,'spa' => 31,'spd' => 31,'spe' => 31],
            'eps' => ['hp' => 0,'atk' => 252,'def' => 0,'spa' => 0,'spd' => 0,'spe' => 252],
            'stats' => ['hp' => 301, 'atk' => 309, 'def' => 166, 'spa' => 140, 'spd' => 187, 'spe' => 394],
            'healt' => 301
        ];

        $defender_pokemon = [
            'id_specie' => 265,
            'pv' => 'jolly',
            'level' => 100,
            'ivs' => ['hp' => 31,'atk' => 31,'def' => 31,'spa' => 31,'spd' => 31,'spe' => 31],
            'eps' => ['hp' => 0,'atk' => 252,'def' => 0,'spa' => 0,'spd' => 0,'spe' => 252],
            'stats' => ['hp' => 301, 'atk' => 383, 'def' => 186, 'spa' => 104, 'spd' => 227, 'spe' => 269],
            'healt' => 301
        ];

        $types = Types::find()->indexBy('id_type')->asArray()->all();

        $species = Species::find()->where(['id_specie' => [$attacking_pokemon['id_specie'],$defender_pokemon['id_specie']]])->indexBy('id_specie')->asArray()->all();

        //a partir de los id's de especie de los pokemones atacante y defensor, esta línea obtiene un arreglo indexado, los nombres de las posiciones de los arreglos serán los id's de especie de los pokemones buscados (atacante y defensivo en este caso), y dentro de esas posiciones un arreglo con los tipos de cada pokemon solicitado
        $species_types = BattleController::indexArray(SpeciesTypes::find()->select('id_specie, id_type')->where([ 'id_specie' => array_keys($species) ])->orderBy('id_specie_type')->asArray()->all(), 'id_specie');

        //de la misma manera indexa las efectividades, en la posición id_type_attack, más la posición id_type_defense podrás encontrar el multiplicador del ataque
        $effectiveness = BattleController::indexEffectiveness(Effectiveness::find()->select('id_type_attack, id_type_defense, multiplier')->where(['id_chart' => 2])->asArray()->all());

        $move = Moves::find()->where(['id_move' => 157])->asArray()->one();

        $stats_names = [ 'attack' => [0 => 'atk', 1 => 'spa'], 'defense' => [0 => 'def', 1 => 'spd']];

        $level = $attacking_pokemon['level'];

        $ataque = $attacking_pokemon['stats'][$stats_names['attack'][$move['category_move']]];

        $poder = $move['power_move'];

        $defensa = $defender_pokemon['stats'][$stats_names['defense'][$move['category_move']]];

        $STAB = ( $move['id_type_move'] == $species_types[$attacking_pokemon['id_specie']][0]['id_type'] || $move['id_type_move'] == $species_types[$attacking_pokemon['id_specie']][1]['id_type'] ) ? 1.5 : 1;

        $efect_tipo_1 = $effectiveness[$move['id_type_move']][$species_types[ $defender_pokemon['id_specie']][0]['id_type']];

        $efect_tipo_2 = $effectiveness[$move['id_type_move']][$species_types[ $defender_pokemon['id_specie']][1]['id_type']];

        $hp_demage = intval( ( intval( intval( intval(2 * $level / 5 + 2) * $ataque * $poder / $defensa) / 50) + 2) * $STAB * $efect_tipo_1 * $efect_tipo_2 * ( rand(217,255)/255 ) );

        $defender_pokemon['healt'] -= $hp_demage;

        return $species[$defender_pokemon['id_specie']]['name_specie']." recibe: ".$hp_demage." pts de daño. La salud de ".$species[$defender_pokemon['id_specie']]['name_specie']." es de ".$defender_pokemon['healt']." de ".$defender_pokemon['stats']['hp'];

        //$hp_demage = ( ( ( ( (2 * level / 5 + 2) * Ataque * Poder / Defensa) / 50) + 2) * STAB * Efec.Tipo1 * Efec.Tipo2 * Rnd / 100);
    }

    static public function indexArray($array, $field)
    {

        if(!is_array($array)) return FALSE;

        $array_indexed = array();

        foreach ($array as $row) {
            $array_indexed[$row[$field]][] = $row;
        }
        return $array_indexed;
    }

    static public function indexEffectiveness($array)
    {

        if(!is_array($array)) return FALSE;

        $array_indexed = array();
        $id_type_attack_act = 0;

        foreach ($array as $row) {

            if ( !isset($array_indexed[$row['id_type_attack']]) )
                $array_indexed[$row['id_type_attack']] = array();

            $array_indexed[$row['id_type_attack']][$row['id_type_defense']] = $row['multiplier'];
        }
        return $array_indexed;
    }
}