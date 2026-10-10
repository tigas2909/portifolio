import java.nio.file.Files;
import java.nio.file.Path;
import java.util.List;

public class VerificaCSV {

    public static void main(String[] args) throws Exception {

        Path arquivo = Path.of("arquivodatabase.csv");

        List<String> linhas = Files.readAllLines(arquivo);

        System.out.println("Total de linhas: " + linhas.size());

        for (int i = 0; i < linhas.size(); i++) {

            String linha = linhas.get(i);
            String[] campos = linha.split(";", -1);

            if (campos.length != 4) {
                System.out.println(
                    "Linha " + (i + 1) +
                    " com problema: " + campos.length + " campos"
                );
            }
        }

        System.out.println("Verificação concluída.");
    }
}