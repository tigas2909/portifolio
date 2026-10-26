<?php 

class ResponseDTO{
    public static function IncomeResponseDto(array $data): string{
        if(data === null){
            return json_encode([
                'success' => false,
                'result' =>  'nao possui dados no item enviado'
            ]);
        }
        return json_encode([
                'success' => true,
                'result' =>  $resultado
            ]);
    }

    public static function TransactionResponseDto(array $data) : string{
        if(data === null){
            return json_encode([
                'success' => false,
                'result' =>  'nao possui dados no item enviado'
            ]);
        }
        return json_encode([
                'success' => true,
                'result' =>  $resultado
            ]);
    }
}