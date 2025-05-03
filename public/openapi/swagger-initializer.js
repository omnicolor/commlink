window.onload = function() {
  window.ui = SwaggerUIBundle({
    url: "/openapi/openapi.yml",
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
