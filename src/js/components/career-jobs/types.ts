export interface JobTerm {
  name: string
  slug: string
}

export interface JobPost {
  id: number
  title: string
  permalink: string
  departments: JobTerm[]
  locations: JobTerm[]
}

export interface JobFilterOption {
  id: number
  slug: string
  name: string
  count: number
}

export interface JobFilterData {
  departments: JobFilterOption[]
  locations: JobFilterOption[]
}

export interface JobFilters {
  department: string
  location: string
  search: string
}

export interface JobApiResponse {
  posts: JobPost[]
  total: number
}
