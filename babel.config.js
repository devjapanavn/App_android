module.exports = {
  presets: ['module:metro-react-native-babel-preset'],
  plugins: [
    [
      'module-resolver',
      {
        root: ['.'],
        extensions: ['.ios.js', '.android.js', '.js', '.jsx', '.js', '.json'],
        alias: {
          '@app/constants': './src/constants',
          '@app/features': './src/features',
          '@app/core': './src/core',
          '@app/assets': './src/assets',
          '@app/utils': './src/utils',
          '@app/api': './src/api',
          '@app/components': './src/components',
          '@app/route': './src/route',
          '@app/store': './src/store',
        },
      },
    ],
    'react-native-reanimated/plugin',
  ],
  env: {
    production: {
      plugins: ['transform-remove-console'],
    },
  },
};
