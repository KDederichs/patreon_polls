import axios from "axios";
import {LoginInput} from "@/types/mutations/User/LoginInput";
import {AuthTokenResponse} from "@/types/AuthTokenResponse";
import { getToken } from '@/state/authState'
import { CreatorConversionResponse } from '@/types/mutations/User/CreatorConversionResponse'
import { ListResponse } from '@/types/ListResponse'
import { OauthInput } from '@/types/mutations/OauthInput'



const publicAxiosInstance = axios.create({
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

const privateAxiosInstance = axios.create({
  headers: {
    Accept: 'application/ld+json',
    'Content-Type': 'application/json',
  },
})
// Request interceptor for API calls
privateAxiosInstance.interceptors.request.use(
  async (config) => {
    const accessToken = getToken()
    if (null !== accessToken) {
      config.headers['Authorization'] = `Bearer ${accessToken}`
    }
    return config
  },
  (error) => {
    void Promise.reject(error)
  },
)


export const oauthConnect = async ({
  code, state
                                   }: OauthInput): Promise<AuthTokenResponse> => {
  const accessToken = getToken()
  const axiosInstance = accessToken !== null ? privateAxiosInstance : publicAxiosInstance;
  return axiosInstance
    .post<AuthTokenResponse>('/oauth/connect', {
    code,
    state
  })
    .then((response) => response.data)
}


export const convertPatreonCreator = async ({
                                     code,
                                   }: LoginInput): Promise<CreatorConversionResponse> => {
  return privateAxiosInstance
    .get<CreatorConversionResponse>(`/creator/oauth/patreon?code=${code}&state=None`, {
      headers: {
        Accept: 'application/json'
      }
    })
    .then((response) => response.data)
}

export const connectPatreon = async ({
                                              code,
                                            }: LoginInput): Promise<AuthTokenResponse> => {
  return privateAxiosInstance
    .get<AuthTokenResponse>(`/oauth/patreon?code=${code}&state=None`, {
      headers: {
        Accept: 'application/json'
      }
    })
    .then((response) => response.data)
}


export const getEntityByIri = async <T>(
  iri: string,
): Promise<T> => {
  return privateAxiosInstance
    .get<T>(iri)
    .then((response) => response.data)
}

export const createEntity = async <T>(
  iri: string,
  data: any,
): Promise<T> => {
  return privateAxiosInstance
    .post<T>(iri, data)
    .then((response) => response.data)
}

export const deleteEntity = async (
  iri: string,
): Promise<void> => {
  return privateAxiosInstance.delete(iri).then((response) => {})
}

export const updateEntity = async <T>(
  iri: string,
  data: any,
): Promise<T> => {
  let config = {
    headers: {
      'Content-Type': 'application/merge-patch+json',
    },
  }
  return privateAxiosInstance
    .patch<T>(iri, data, config)
    .then((response) => response.data)
}

export const getHydraList = async <T>(
  iri: string,
): Promise<ListResponse<T>> => {
  return privateAxiosInstance
    .get<ListResponse<T>>(iri)
    .then((response) => response.data)
}

export const getListEntities = async <T>(
  iri: string,
): Promise<T[]> => {
  return getHydraList<T>(iri).then(
    (response) => response['member'],
  )
}
