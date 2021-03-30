<?php

    namespace Admisiones\Controller;

    include dirname(__FILE__) . '\..\models\Paciente.php';

    use Admisiones\Models\Pacientes;
    use DateTime;
    use Exception;

    /*
     * Datos socioeconomicos de un paciente.
     *
     * @author Edier Andres Villaneda Navarro
     * @version 1.0
     */

    class DatosSocioEconomicosController extends Pacientes
    {

        private $errorCode;
        private $error;
        public $request;
        private $response;

        /**
         * Consultar los datos socioeconomicos de un paciente.
         * @param object $conex          Datos de la conexión a la base de datos. (Obligatorio)
         * @param string $empresa        Alias de la empresa
         * @param        $numeroHistoria Número de Historia Clinica del paciente.
         * @param        $numeroIngreso  Numero de ingreso del paciente.
         */
        public function __construct($conex, string $empresa, $numeroHistoria, $numeroIngreso)
        {
            $this->setNumeroHistoria($numeroHistoria);
            $this->setNumeroIngreso($numeroIngreso);
            $this->error = "";
            if ($this->validarDatos()) {
                $this->getDatosPaciente($conex, $empresa);
            }
        }

        function jsonSerialize(): array
        {
            return [
                'numeroHistoria' => $this->getNumeroHistoria(),
                'numeroIngreso' => $this->getNumeroIngreso(),
                'documento' => $this->getDocumento(),
                'tipoDocumento' => $this->getTipoDocumento(),
                'numeroDocumento' => $this->getNumeroDocumento(),
                'nombreCompleto' => $this->getNombreCompleto(),
                'nombres' => $this->getNombres(),
                'apellidos' => $this->getApellidos(),
                'aseguradora' => $this->getAseguradora(),
                'diagnostico' => $this->getDiagnostico(),
//                'edad' => $this->getEdad()
            ];

        }

        /**
         * Validar si los numeros de Historia e Ingreso son validos.
         * @return boolean
         */
        public function validarDatos(): bool
        {
            $this->errorCode = false;
            switch (true) {
                case $this->getNumeroHistoria() == 0:
                    $this->errorCode = true;
                    $this->error = "Número de Historia invalido";
                    //                throw new \Exception("Número de Historia invalido");
                    break;
                case $this->getNumeroIngreso() == 0:
                    $this->errorCode = true;
                    $this->error = "Número de Ingreso invalido";
                    //                throw new \Exception("Número de Ingreso invalido");
                    break;
            }
            if ($this->error != "") {
                return false;
            }
            return true;
        }

        /**
         * @param $conex
         * @param $empresa
         */
        public function getDatosPaciente($conex, $empresa)
        {
            $query = "SELECT pac.Pactdo AS tipodocumento, pac.Pacdoc as documento, "
                . "pac.Pacno1 AS nombre, pac.Pacno2 AS segundo_nombre, "
                . "pac.Pacap1 AS primer_apellido, pac.Pacap2 AS segundo_apellido, "
                . "ing.Ingdig AS diagnostico, aseg.Empnom AS aseguradora, pac.Pacfna AS fecha_nacimiento, pac.Pacsex as sexo "
                . "FROM matrix.{$empresa}_000101 AS ing "
                . "INNER JOIN matrix.{$empresa}_000100 AS pac ON pac.Pachis = ing.inghis "
                . "INNER JOIN matrix.{$empresa}_000105 AS est ON pac.Pacest = est.Selcod AND est.seltip = 25 "
                . "INNER JOIN matrix.{$empresa}_000024 AS aseg ON ing.Ingcem = aseg.Empcod "
                . "WHERE ing.Inghis = ? AND ing.Ingnin = ?";

            try {
                $sql = mysqli_prepare($conex, $query);

                $sql->bind_param("ii", $this->numeroHistoria, $this->numeroIngreso);
                $sql->execute();

                $sql->bind_result($this->tipoDocumento, $this->numeroDocumento,
                    $this->nombre1, $this->nombre2,
                    $this->apellido1, $this->apellido2,
                    $this->diagnostico, $this->aseguradora,
                    $this->fechaNacimiento, $this->genero);

                $sql->fetch();
            } catch (Exception $exc) {
                $this->error = $exc->error;
            }
        }

        function getError(): string
        {

            return $this->error;
        }

    }
