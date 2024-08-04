import axios from "axios";
import { TestPortalApi } from "./api-client/api";
import { Configuration } from "./api-client";

axios.defaults.baseURL = "/";
const api = axios.create({ baseURL: "/" });
api.interceptors.response.use(
	(response) => response,
	(error) => {
		if (error.response && error.response.status === 401) {
			window.location.reload();
			return Promise.reject(error);
		}
		return Promise.reject(error);
	}
);

const adminApi = new TestPortalApi(
	new Configuration({
		accessToken: () => "",
	}),
	undefined,
	api
);

export { adminApi };
