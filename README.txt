# SchemaChef

![WordPress Compatibility](https://img.shields.io/wordpress/v/schemachef?style=flat-square)
![Plugin Version](https://img.shields.io/github/v/release/mehdibafdil-dev/schemachef?style=flat-square)
![License](https://img.shields.io/github/license/mehdibafdil-dev/schemachef?style=flat-square)

## 🍳 Description

SchemaChef is a powerful WordPress plugin that simplifies the implementation of Recipe Schema Markup for your food blog or recipe website. By automatically generating structured data, it enhances your recipes' visibility in search results and improves your overall SEO performance.

### ✨ Key Features

- 🚀 One-click Recipe Schema Markup implementation
- 📊 SEO-optimized structured data for recipes
- 🎨 User-friendly interface in WordPress editor
- ⚡ Lightweight and performance-optimized
- 🔄 Compatible with major SEO plugins
- 🌐 Internationalization ready

## 📦 Installation

1. Upload the `schemachef` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings through the WordPress editor

## 🏗️ Plugin Architecture

Our plugin follows a modern, modular architecture:

```text
schemachef/
├── admin/                  # Admin-specific functionality
│   ├── css/               # Admin styles
│   ├── js/               # Admin scripts
│   └── class-schemachef-admin.php
├── public/                # Public-facing functionality
│   ├── css/              # Public styles
│   ├── js/               # Public scripts
│   └── class-schemachef-public.php
├── includes/             # Core plugin functionality
│   ├── class-schemachef.php
│   ├── class-schemachef-activator.php
│   ├── class-schemachef-deactivator.php
│   └── class-schemachef-i18n.php
└── templates/            # Twig template files
```

## 🚀 Usage

1. After activation, navigate to any post or page
2. Look for the SchemaChef section in the editor
3. Fill in your recipe details
4. Publish - Schema markup is automatically added!

## ❓ FAQ

### How do I configure Recipe Schema Markup?
Simply edit any post or page, fill in the recipe details in the SchemaChef section, and publish. The plugin handles the schema markup automatically.

### Is this plugin compatible with other SEO plugins?
Yes! SchemaChef is designed to work seamlessly with popular SEO plugins like Yoast SEO, Rank Math, and others.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a history of changes.

## 💬 Support

Need help? Contact us at mehdibafdil@gmail.com

## 🙏 Acknowledgments

Special thanks to the WordPress community and all contributors who help make this plugin better.

