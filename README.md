# Timeline.co.zw - Music Charts & Entertainment Platform

A responsive web application that replicates the Billboard Hot 100 website with additional features for music charts, videos, richest people, awards, and business analytics.

## 🚀 Features

### Pages
- **Homepage** - Hero section with animations, featured charts, and trending content
- **Charts** - Billboard Hot 100 style music charts with filtering and sorting
- **Videos** - Top 100 music videos with grid/list view toggle
- **Richest People** - Top 100 richest people with wealth tracking
- **Awards** - Music awards and recognition showcase
- **Business** - Music industry business charts and analytics

### Key Features
- ✨ **Responsive Design** - Works perfectly on desktop, tablet, and mobile
- 🎨 **Modern UI/UX** - Clean, professional design inspired by Billboard
- ⚡ **Smooth Animations** - CSS animations and transitions throughout
- 🔍 **Advanced Filtering** - Filter content by category, genre, time period
- 📊 **Interactive Charts** - Visual data representation with hover effects
- 🎵 **Music Integration Ready** - Prepared for API integration
- 📱 **Mobile-First** - Optimized for mobile devices
- ♿ **Accessible** - WCAG compliant with keyboard navigation

## 🛠️ Technology Stack

- **HTML5** - Semantic markup
- **CSS3** - Modern styling with Flexbox and Grid
- **JavaScript (ES6+)** - Interactive functionality
- **Font Awesome** - Icons
- **Google Fonts** - Typography (Inter font family)

## 📁 Project Structure

```
timeline.co.zw/
├── index.html              # Homepage
├── charts.html             # Music charts page
├── videos.html             # Top videos page
├── richest.html            # Richest people page
├── awards.html             # Awards page
├── business.html           # Business charts page
├── css/
│   ├── style.css           # Main styles
│   ├── animations.css      # Animation keyframes
│   ├── charts.css          # Charts page styles
│   ├── videos.css          # Videos page styles
│   ├── richest.css         # Richest page styles
│   ├── awards.css          # Awards page styles
│   ├── business.css        # Business page styles
│   └── responsive.css      # Responsive design
├── js/
│   ├── main.js             # Core functionality
│   ├── animations.js       # Animation controller
│   ├── charts.js           # Charts page logic
│   ├── videos.js           # Videos page logic
│   ├── richest.js          # Richest page logic
│   ├── awards.js           # Awards page logic
│   └── business.js         # Business page logic
├── images/                 # Image assets
└── README.md              # Project documentation
```

## 🎨 Design Features

### Color Scheme
- **Primary**: #00d4aa (Teal)
- **Secondary**: #00b894 (Dark Teal)
- **Accent**: #667eea (Purple Blue)
- **Background**: #f8f9fa (Light Gray)
- **Text**: #1a1a1a (Dark Gray)

### Typography
- **Font Family**: Inter (Google Fonts)
- **Weights**: 300, 400, 500, 600, 700, 800
- **Responsive**: Scales appropriately across devices

### Animations
- **Hero Animations**: Staggered text reveals, floating particles
- **Scroll Animations**: Intersection Observer API
- **Hover Effects**: Smooth transitions and transforms
- **Loading States**: Spinners and skeleton screens

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px
- **Large Desktop**: > 1400px

## 🔧 Setup Instructions

1. **Clone or Download** the project files
2. **Open** `index.html` in a web browser
3. **Navigate** between pages using the header navigation
4. **Test** responsive design by resizing the browser window

## 🚀 Future Enhancements

### API Integration
The project is structured to easily integrate with real APIs:

```javascript
// Example API integration
async function fetchChartData(chartType) {
    const response = await fetch(`/api/charts/${chartType}`);
    return await response.json();
}
```

### Planned Features
- 🎵 **Real-time Data** - Live chart updates
- 🎧 **Music Player** - Embedded audio player
- 📊 **Advanced Analytics** - Detailed metrics and insights
- 🔐 **User Accounts** - Personalization and favorites
- 📱 **PWA Support** - Progressive Web App capabilities
- 🌐 **Internationalization** - Multi-language support

## 🎯 Performance Optimizations

- **Lazy Loading** - Images and content loaded on demand
- **Minified Assets** - Optimized CSS and JavaScript
- **Efficient Animations** - Hardware-accelerated transforms
- **Responsive Images** - Appropriate sizing for different devices
- **Caching Strategy** - Browser caching for static assets

## ♿ Accessibility Features

- **Keyboard Navigation** - Full keyboard support
- **Screen Reader** - Semantic HTML and ARIA labels
- **High Contrast** - Support for high contrast mode
- **Reduced Motion** - Respects user motion preferences
- **Focus Indicators** - Clear focus states for all interactive elements

## 🧪 Browser Support

- **Chrome** 90+
- **Firefox** 88+
- **Safari** 14+
- **Edge** 90+

## 📄 License

This project is created for educational and portfolio purposes. Feel free to use and modify as needed.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test across different devices
5. Submit a pull request

## 📞 Contact

For questions or suggestions, please reach out through the project repository.

---

**Built with ❤️ for the music industry**
