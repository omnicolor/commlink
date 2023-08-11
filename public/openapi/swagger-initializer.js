window.onload = function() {
  window.ui = SwaggerUIBundle({
    url: "https://commlink.digitaldarkness.com/openapi.yml",
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "BaseLayout",
    tryItOutEnabled: false
  });
};
