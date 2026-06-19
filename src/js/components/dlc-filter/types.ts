export interface FilterOption {
  id: number
  slug: string
  name: string
  count: number
}

export interface FilterData {
  categories: FilterOption[]
  includes: FilterOption[]
  waterways: FilterOption[]
}

export interface DlcCategory {
  name: string
  slug: string
}

export interface DlcPost {
  id: number
  title: string
  excerpt?: string
  permalink: string
  thumbnail?: string
  category?: DlcCategory
  categories?: DlcCategory[]
  includes: string[]
  includes_count: number
}

export interface Filters {
  category: string
  include: string[]
  waterway: string[]
  sort: string
  search: string
}

export interface DlcApiResponse {
  posts: DlcPost[]
  total: number
}
