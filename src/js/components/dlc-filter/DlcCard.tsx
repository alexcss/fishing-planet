import React, { Fragment } from 'react'
import type { DlcPost } from './types'

interface DlcCardProps {
  dlc: DlcPost
}

const DlcCard: React.FC<DlcCardProps> = ({ dlc }) => {
  return (
    <article className="group border-b border-white/15 pb-32">
      <div className="grid grid-cols-1 gap-32 md:gap-40 lg:grid-cols-24 lg:gap-40">
        {/* Content Side */}
        <div className="space-y-20 md:space-y-24 lg:col-span-9 xl:col-span-7">
          {/* Tags - All Categories with Popular highlighted */}
          <div className="flex flex-wrap items-center gap-8 xl:gap-x-16">
            {dlc.categories && dlc.categories.length > 0 ? (
              dlc.categories.map((cat, index) => (
                <Fragment key={cat.slug}>
                  {cat.name.toLowerCase() == 'popular' ? (
                    <span className={`fp-badge fp-btn-corners w-auto border border-white/15 bg-black/20`}>
                      <span>{cat.name}</span>
                    </span>
                  ) : (
                    <span className={`fp-badge`}>{cat.name}</span>
                  )}
                </Fragment>

              ))
            ) : dlc.category ? (
              <span className={`fp-badge`}>{dlc.category.name}</span>
            ) : null}
          </div>

          {/* Title */}
          <h3 className="h4 group-hover:text-accent text-white transition-colors">
            <a href={dlc.permalink} className="no-underline">
              {dlc.title}
            </a>
          </h3>

          {/* Excerpt */}
          {dlc.excerpt && <p className="fp-text-body-small line-clamp-6 text-white">{dlc.excerpt}</p>}

          {/* Includes Section */}
          {dlc.includes && dlc.includes.length > 0 && (
            <div className="pt-8">
              <h4 className="fp-text-small-description mb-16">Pack Content</h4>
              <ul className="fp-list-square fp-text-body-small space-y-4 text-white/80">
                {dlc.includes.map((item, index) => (
                  <li key={index} dangerouslySetInnerHTML={{ __html: item }} />
                ))}
              </ul>
              {dlc.includes_count > 3 && (
                <div className="fp-text-body-small text-accent mt-12 flex items-center gap-6">
                  <svg className="h-16 w-16">
                    <use href="#icon-plus" />
                  </svg>
                  And more
                </div>
              )}
            </div>
          )}
        </div>

        {/* Image Side */}
        <div className="lg:col-span-15 lg:col-span-17">
          <a href={dlc.permalink} className="md:fp-block-corners relative block">
            <picture className="block overflow-hidden">
              {dlc.thumbnail ? (
                <img
                  src={dlc.thumbnail}
                  alt={dlc.title}
                  className="aspect-2560/1440 h-auto w-full object-cover transition-transform duration-500 group-hover:scale-101"
                  loading="lazy"
                />
              ) : (
                <div className="bg-gray-gunmetal flex aspect-2560/1440 items-center justify-center">
                  <span className="font-heading text-24 text-white/30">No Image</span>
                </div>
              )}
            </picture>
          </a>
        </div>
      </div>
    </article>
  )
}

export default DlcCard
