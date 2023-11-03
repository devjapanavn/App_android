import RNFS from 'react-native-fs';
const videoUrlCopy = async (uri, fileName) => {
	const destPath = `${RNFS.TemporaryDirectoryPath}/${fileName}`;
	await RNFS.copyFile(uri, destPath);
	await RNFS.stat(destPath);
	return destPath;
};
function getExtension(path) {
	var basename = path.split(/[\\/]/).pop(), // extract file name from full path ...
		// (supports `\\` and `/` separators)
		pos = basename.lastIndexOf('.'); // get last position of `.`

	if (basename === '' || pos < 1) {
		return null;
	} //  `.` not found (-1) or comes first (0)

	return basename.slice(pos + 1); // extract extension ignoring `.`
}

export { videoUrlCopy, getExtension };
