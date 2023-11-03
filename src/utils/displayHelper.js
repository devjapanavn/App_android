import {useWindowDimensions} from 'react-native';

const useScreenDimensions = () => {
  const screenData = useWindowDimensions();

  return {
    ...screenData,
    isLandscape: screenData.width > screenData.height,
  };
};
export {useScreenDimensions};
