import React from 'react'
import type { FilterOption } from './types'

interface FilterAccordionProps {
  label: string
  isOpen: boolean
  onToggle: () => void
  options: FilterOption[]
  selected: string | string[]
  multi?: boolean
  onChange: (value: string | string[]) => void
}

const FilterAccordion: React.FC<FilterAccordionProps> = ({ label, isOpen, onToggle, options, selected, multi = false, onChange }) => {
  const isAllSelected = multi ? (selected as string[]).length === 0 : selected === ''
  const selectedCount = multi ? (selected as string[]).length : selected !== '' ? 1 : 0
  const singleSelectedName =
    selectedCount === 1 ? (options.find((o) => (multi ? (selected as string[]).includes(o.slug) : o.slug === selected))?.name ?? null) : null

  const handleSelect = (slug: string) => {
    if (!multi) {
      onChange(slug)
      return
    }
    const current = selected as string[]
    const next = current.includes(slug) ? current.filter((s) => s !== slug) : [...current, slug]
    onChange(next)
  }

  const isSelected = (slug: string) => (multi ? (selected as string[]).includes(slug) : selected === slug)

  return (
    <div>
      <button onClick={onToggle} className="bg-gray-gunmetal flex w-full items-center justify-between gap-6 border border-white/15 p-12">
        <span className="fp-captital-title" dangerouslySetInnerHTML={{ __html: singleSelectedName ?? label }} />
        {selectedCount > 1 && <span className="font-heading text-24/none ml-auto text-white/50">({selectedCount})</span>}
        <svg className={`text-gray h-24 w-24 transition-transform ${isOpen ? 'rotate-180' : ''}`}>
          <use href="#icon-arrow-down" />
        </svg>
      </button>
      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden px-12">
          <div className="py-18">
            <button
              onClick={() => onChange(multi ? [] : '')}
              className={`font-heading text-24 flex w-full items-center justify-between border-b border-white/15 pb-10 text-left uppercase ${isAllSelected ? 'text-white' : 'text-white/70'}`}
            >
              <span>All</span>
              {isAllSelected && (
                <svg className="h-24 w-24 text-white">
                  <use href="#icon-check" />
                </svg>
              )}
            </button>
            {options.map((opt) => (
              <button
                key={opt.slug}
                onClick={() => handleSelect(opt.slug)}
                className={`font-heading text-24/none flex w-full items-center justify-between border-b border-white/15 py-16 text-left uppercase transition-colors ${isSelected(opt.slug) ? 'text-white' : 'text-white/70'}`}
              >
                <span dangerouslySetInnerHTML={{ __html: opt.name }} />
                {isSelected(opt.slug) && (
                  <svg className="h-24 w-24 text-white">
                    <use href="#icon-check" />
                  </svg>
                )}
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

export default FilterAccordion
