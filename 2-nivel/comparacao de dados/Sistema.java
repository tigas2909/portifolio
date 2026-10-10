import java.nio.file.Files;
import java.nio.file.Path;
import java.util.ArrayList;

public class Sistema {
    public static void main(String[] args) throws Exception {
        System.out.println("iniciou!!!!");
        Path arqDataPath = Path.of("arquivodatabase.csv");
        Path bancoPath = Path.of("banco.csv");
        Path resultadoPath = Path.of("resultado.csv");

        ArrayList<String> banco = new ArrayList<>(
            Files.readAllLines(bancoPath)
        );

        ArrayList<String> arq = new ArrayList<>(
            Files.readAllLines(arqDataPath)
        );

        for (int b = 0; b < banco.size(); ++b) {

            for (int a = 0; a < arq.size(); ++a) {

                String[] ban = banco.get(b).split(";");
                String[] ar = arq.get(a).split(";");
                //System.out.println("BAN: [" + ban[2] + "] ARQ: [" + ar[2] + "]");
                if (ban[2].trim().equals(ar[2].trim())) {
                    System.out.println(ban[0]);
                    banco.remove(b);
                    b--;
                    break;
                }
            }
        }
        Files.write(resultadoPath, banco);
    }
}