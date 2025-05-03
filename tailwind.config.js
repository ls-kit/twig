module.exports = {
  content: [
    "./templates/**/*.twig",
    "./build/**/*.html"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#2563EB',    // More vibrant blue
        accent: '#F59E0B',     // Warmer amber
        darkbg: '#0F172A',     // Deep navy (better than pure black)
        lightbg: '#1E293B',    // Soft slate
        text: '#E2E8F0',       // Off-white
        success: '#22C55E'     // Adding green for CTAs
      }
    },
  },
  plugins: [],
};
