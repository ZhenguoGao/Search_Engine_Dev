
/**
 * Created by Martina on 4/17/17.
 */
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.PrintWriter;
import org.apache.tika.exception.TikaException;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.html.HtmlParser;
import org.apache.tika.sax.BodyContentHandler;

import org.xml.sax.SAXException;

public class wordFrequency {

    public static void main(final String[] args) throws IOException,SAXException, TikaException {

        File dir = new File("Users/Zhenguo/Desktop/CNNData/CNNDownloadData/");
        PrintWriter writer = new PrintWriter("big.txt", "UTF-8");

        for (File file : dir.listFiles()){
            BodyContentHandler handler = new BodyContentHandler(-1);
            Metadata metadata = new Metadata();
            FileInputStream inputstream = new FileInputStream(file);
            ParseContext pcontext = new ParseContext();
            HtmlParser htmlparser = new HtmlParser();
            htmlparser.parse(inputstream, handler, metadata,pcontext);
            writer.println(handler.toString());

        }

        writer.flush();
        writer.close();
    }
}