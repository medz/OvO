import '../schema.dart';
import '../transform.dart';

extension OvoStringTransform on OvoSchema<String> {
  OvoSchema<String> trim() => transform((context, data) => data.trim());

  OvoSchema<String> trimLeft() => transform((context, data) => data.trimLeft());

  OvoSchema<String> trimRight() =>
      transform((context, data) => data.trimRight());

  OvoSchema<String> toLowerCase() =>
      transform((context, data) => data.toLowerCase());

  OvoSchema<String> toUpperCase() =>
      transform((context, data) => data.toUpperCase());

  OvoSchema<String> replaceAll(Pattern from, String to) =>
      transform((context, data) => data.replaceAll(from, to));

  OvoSchema<String> replaceFirst(Pattern from, String to) =>
      transform((context, data) => data.replaceFirst(from, to));

  OvoSchema<String> padLeft(int width, [String padding = ' ']) =>
      transform((context, data) => data.padLeft(width, padding));

  OvoSchema<String> padRight(int width, [String padding = ' ']) =>
      transform((context, data) => data.padRight(width, padding));
}
