import React, { useState } from 'react'
import { useWaterwayGroups, groupOrder } from './useWaterwayGroups'
import type { FilterOption } from './types'

interface MobileWaterwayFilterProps {
  options: FilterOption[]
  selected: string[]
  onChange: (value: string[]) => void
  isOpen?: boolean
  onToggle?: () => void
  label?: string
}

const MobileWaterwayFilter: React.FC<MobileWaterwayFilterProps> = ({ options, selected, onChange, isOpen, onToggle, label = 'Waterway' }) => {
  const [searchQuery, setSearchQuery] = useState('')
  const [openGroups, setOpenGroups] = useState<Record<string, boolean>>(() => {
    const initial: Record<string, boolean> = {}
    groupOrder.forEach((name) => (initial[name] = false))
    return initial
  })

  const filteredGroups = useWaterwayGroups(options, searchQuery)

  const toggleGroup = (name: string) => {
    setOpenGroups((prev) => ({ ...prev, [name]: !prev[name] }))
  }

  const isSelected = (slug: string) => selected.includes(slug)

  const toggleSelection = (slug: string) => {
    onChange(isSelected(slug) ? selected.filter((s) => s !== slug) : [...selected, slug])
  }

  const selectedCount = selected.length
  const isExpanded = onToggle ? !!isOpen : true

  return (
    <div>
      {onToggle && (
        <button onClick={onToggle} className="bg-gray-gunmetal flex w-full items-center justify-between gap-6 border border-white/15 p-12">
          <span className="fp-captital-title">{selectedCount > 0 ? `${label} (${selectedCount})` : label}</span>
          <svg className={`text-gray h-24 w-24 transition-transform ${isOpen ? 'rotate-180' : ''}`}>
            <use href="#icon-arrow-down" />
          </svg>
        </button>
      )}

      <div className={`fp-collapse ${isExpanded ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="space-y-4 pt-8">
            <form
              onSubmit={(e) => e.preventDefault()}
              className="fp-btn-corners flex h-64 w-full items-center justify-between gap-12 border border-white/15 bg-black px-16 py-20 text-left"
            >
              <span className="flex min-w-0 flex-1 items-center">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search waterway"
                  className="fp-btn-text min-w-0 flex-1 bg-transparent text-white/50 uppercase placeholder:text-white/30 focus:outline-none"
                />
              </span>
              <svg className="h-24 w-24 shrink-0 text-white">
                <use href="#icon-search" />
              </svg>
            </form>

            <button onClick={() => onChange([])} className="block w-full px-12 hover:bg-white/5">
              <span className="flex items-center justify-between border-b border-white/15 py-16">
                <span className={`fp-captital-title-sm ${selectedCount === 0 ? 'text-white' : 'text-white/70'}`}>All</span>
                {selectedCount === 0 && (
                  <svg className="h-24 w-24 text-white">
                    <use href="#icon-check" />
                  </svg>
                )}
              </span>
            </button>

            {filteredGroups.map((group) => (
              <div key={group.name}>
                <button onClick={() => toggleGroup(group.name)} className="block w-full px-12 hover:bg-white/5">
                  <span className="flex items-center justify-between py-16">
                    <span className="fp-captital-title-sm text-sand flex items-center gap-4">
                      <span>{group.name}</span>
                      <span className="opacity-75">({group.options.length})</span>
                    </span>
                    <svg className={`text-gray h-24 w-24 transition-transform ${openGroups[group.name] ? 'rotate-180' : ''}`}>
                      <use href="#icon-arrow-down" />
                    </svg>
                  </span>
                </button>

                <div className={`fp-collapse ${openGroups[group.name] ? 'open' : ''}`}>
                  <div className="overflow-hidden">
                    <div className="space-y-16 pt-4 pr-12 pb-16 pl-32">
                      {group.options.map((option) => (
                        <button
                          key={option.slug}
                          onClick={() => toggleSelection(option.slug)}
                          className="group flex min-h-24 w-full items-center justify-between text-white/70 hover:text-white"
                        >
                          <span className={`fp-captital-title-sm flex items-center gap-12 ${isSelected(option.slug) ? 'text-white' : ''}`}>
                            <span className="size-8 shrink-0 bg-white/15 transition-colors group-hover:bg-white"></span>
                            {option.name}
                          </span>
                          {isSelected(option.slug) && (
                            <svg className="h-24 w-24 text-white">
                              <use href="#icon-check" />
                            </svg>
                          )}
                        </button>
                      ))}
                    </div>
                  </div>
                </div>

                <div className="mx-12 h-0 w-full border-b border-white/15"></div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

export default MobileWaterwayFilter
