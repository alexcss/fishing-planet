import { useMemo } from 'react'
import type { FilterOption } from './types'

export type WaterwayGroup = {
  name: string
  options: FilterOption[]
}

export const groupOrder = ['Lakes', 'Oceans', 'Rivers', 'Ponds', 'Other']

const getGroupName = (name: string): string => {
  const lower = name.toLowerCase()
  if (lower.includes('lake')) return 'Lakes'
  if (lower.includes('ocean')) return 'Oceans'
  if (lower.includes('river')) return 'Rivers'
  if (lower.includes('pond')) return 'Ponds'
  return 'Other'
}

const stripTypeWord = (name: string, group: string): string => {
  const typeWord = group.toLowerCase().replace(/s$/, '')
  return name.replace(new RegExp(`\\s*${typeWord}s?\\s*`, 'i'), '').trim()
}

export const useWaterwayGroups = (options: FilterOption[], searchQuery: string): WaterwayGroup[] => {
  return useMemo(() => {
    const map = new Map<string, FilterOption[]>()
    options.forEach((opt) => {
      const groupName = getGroupName(opt.name)
      const displayName = stripTypeWord(opt.name, groupName)
      const existing = map.get(groupName) || []
      map.set(groupName, [...existing, { ...opt, name: displayName }])
    })

    const groups = groupOrder
      .filter((name) => name === 'Other' || map.has(name))
      .map((name) => ({ name, options: map.get(name) || [] }))

    if (!searchQuery) return groups

    const q = searchQuery.toLowerCase()
    return groups
      .map((g) => ({ ...g, options: g.options.filter((o) => o.name.toLowerCase().includes(q)) }))
      .filter((g) => g.options.length > 0)
  }, [options, searchQuery])
}
