package application;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.EnableWebMvc;
import org.thymeleaf.spring5.SpringTemplateEngine;
import org.thymeleaf.templatemode.TemplateMode;
import org.thymeleaf.templateresolver.ITemplateResolver;
import org.thymeleaf.templateresolver.StringTemplateResolver;

@Configuration
@EnableWebMvc
public class ThymeleafConfiguration {
	@Bean
	public ITemplateResolver createTemplateResolver(){
		StringTemplateResolver templateResolver = new StringTemplateResolver();
	    templateResolver.setTemplateMode(TemplateMode.HTML);
	    templateResolver.setCacheable(false);
	    return templateResolver;
	}

	@Bean
	public SpringTemplateEngine templateEngine(){
	    SpringTemplateEngine templateEngine = new SpringTemplateEngine();
	    templateEngine.setTemplateResolver(createTemplateResolver());
	    templateEngine.setEnableSpringELCompiler(true);
	    return templateEngine;
	}
}
