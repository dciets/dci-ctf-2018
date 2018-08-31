package application;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Map;
import java.util.Map.Entry;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.sqlite.SQLiteConfig;
import org.thymeleaf.TemplateEngine;
import org.thymeleaf.templatemode.TemplateMode;
import org.thymeleaf.templateresolver.StringTemplateResolver;

@Controller
public class PageController {
	private TemplateEngine templateEngine;
	private Connection connection;
	
	public PageController() {
		StringTemplateResolver templateResolver = new StringTemplateResolver();
		templateResolver.setTemplateMode(TemplateMode.HTML);
		templateEngine = new TemplateEngine();
		templateEngine.setTemplateResolver(templateResolver);
		
		SQLiteConfig config = new SQLiteConfig();
		config.setReadOnly(true);
		String url = "jdbc:sqlite:/db/templates.db";
		
		try {
			Connection connection = DriverManager.getConnection(url, config.toProperties());
			this.connection = connection;
		} catch(SQLException e) {
			System.err.println(e.getMessage());
		}
	}
	
	@GetMapping("/")
	public String renderPage(@RequestParam(name="page", required=false, defaultValue="") String page,
			@RequestParam(name = "type", required=false, defaultValue="") String type,
			Model model,
			@RequestParam Map<String,String> allRequestParams) {
		if(page.equals("") && type.equals("")) {
			return "<html><head><meta http-equiv=\"Refresh\" content=\"0; url=/?page=index&type=content\" /></head></html>";
		}
		
		String html;
		
		try {
			ResultSet rs = connection.createStatement().executeQuery("SELECT html FROM templates WHERE page='" + page +
					"' AND type='" + type + "'");
			html = rs.getString("html");
			rs.close();
		} catch(SQLException e) {
			System.err.println(e.getMessage());
			return "<html><head><meta http-equiv=\"Refresh\" content=\"0; url=/?page=index&type=content\" /></head></html>";
		}
		
		for(Entry<String, String> entry: allRequestParams.entrySet()) {
			if(!entry.getKey().equals("page") && !entry.getKey().equals("type")) {
				model.addAttribute(entry.getKey(), entry.getValue());
			}
		}
		
		return html;
	}
}