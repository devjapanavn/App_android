import { colors } from "@app/assets";

export const InteractiveStatus = {
	pending: 0,
	approved: 1
};

export const InteractiveStatusString = value => {
	switch (value) {
		case InteractiveStatus.pending:
			return 'Chờ duyệt';
		case InteractiveStatus.approved:
			return 'Đã duyệt';
		default:
			return 'Chờ duyệt';
	}
};

export const InteractiveStatusColor = value => {
	switch (value) {
		case InteractiveStatus.pending:
			return colors.orange;
		case InteractiveStatus.approved:
			return colors.cyan;
		default:
			return colors.orange;
	}
};
